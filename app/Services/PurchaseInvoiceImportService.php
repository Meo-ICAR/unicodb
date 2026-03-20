<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Client;
use App\Models\Principal;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseInvoiceImportService
{
    protected $companyId;
    protected $filename;

    protected $importResults = [
        'imported' => 0,
        'updated' => 0,
        'errors' => 0,
        'skipped' => 0,
        'details' => []
    ];

    public function __construct($companyId = null, $filename = null)
    {
        $this->companyId = $companyId;
        $this->filename = $filename;
    }

    /**
     * Import purchase invoices from CSV/Excel file
     *
     * @param string $filePath Path to the file
     * @param string $companyId Company ID to assign to invoices
     * @return array Import results
     */
    public function import(string $filePath, string $companyId = null): array
    {
        $this->companyId = $companyId ?: $this->companyId;

        // Extract filename from path if not provided
        if (!$this->filename) {
            $this->filename = basename($filePath);
        }

        $this->importResults = [
            'imported' => 0,
            'updated' => 0,
            'errors' => 0,
            'skipped' => 0,
            'details' => [],
            'filename' => $this->filename,
        ];

        try {
            // Use direct CSV parsing for better control
            $this->importFromCSV($filePath);

            // Match agents by VAT number after import
            $this->matchAgentsByVatNumber();

            // Match clients by VAT number after import
            $this->matchClientsByVatNumber();

            Log::info('Purchase invoices import completed', [
                'file' => $filePath,
                'company_id' => $this->companyId,
                'results' => $this->importResults
            ]);

            return $this->importResults;
        } catch (\Exception $e) {
            Log::error('Purchase invoices import failed', [
                'file' => $filePath,
                'company_id' => $this->companyId,
                'error' => $e->getMessage()
            ]);

            $this->importResults['success'] = false;
            $this->importResults['message'] = $e->getMessage();

            return $this->importResults;
        }
    }

    protected function importFromCSV(string $filePath)
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception("Cannot open file: $filePath");
        }

        // Read headers
        $headers = fgetcsv($handle, 0, ';');
        if (!$headers) {
            fclose($handle);
            throw new \Exception('Cannot read headers from file');
        }

        // Clean headers - remove special characters and normalize
        $cleanHeaders = [];
        foreach ($headers as $header) {
            $cleanHeader = trim($header);
            // Remove BOM if present
            $cleanHeader = str_replace("\u{FEFF}", '', $cleanHeader);
            $cleanHeader = str_replace(['.', ' ', '-', '(', ')'], ['_', '_', '_', '_', '_'], $cleanHeader);
            $cleanHeader = strtolower($cleanHeader);
            $cleanHeaders[] = $cleanHeader;
        }

        Log::info('CSV Headers', ['original' => $headers, 'cleaned' => $cleanHeaders]);

        $rowNumber = 2;  // Start from 2 since we already read header
        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                $this->processRow($row, $cleanHeaders, $rowNumber);
                $rowNumber++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        fclose($handle);
    }

    protected function processRow(array $row, array $headers, int $rowNumber)
    {
        try {
            // Use direct index mapping instead of array_combine for reliability
            $rowData = [];
            foreach ($headers as $index => $header) {
                $rowData[$header] = $row[$index] ?? '';
            }

            // Debug first few rows
            static $debugCount = 0;
            if ($debugCount < 3) {
                Log::info("Debug row $debugCount (row $rowNumber)", [
                    'headers' => $headers,
                    'row' => $row,
                    'rowData' => $rowData,
                    'nr_value' => $rowData['nr_'] ?? 'NOT_FOUND',
                    'fornitore_value' => $rowData['fornitore'] ?? 'NOT_FOUND',
                    'check_nr_empty' => empty($rowData['nr_']),
                    'check_fornitore_empty' => empty($rowData['fornitore'])
                ]);
                $debugCount++;
            }

            // Skip empty rows
            if (empty($rowData['nr_']) || empty($rowData['fornitore'])) {
                Log::info("Skipping row $rowNumber", [
                    'nr_' => $rowData['nr_'],
                    'fornitore' => $rowData['fornitore']
                ]);
                $this->importResults['skipped']++;
                return;
            }

            $invoiceData = $this->mapRowToInvoiceData($rowData);

            // Add company_id
            $invoiceData['company_id'] = $this->companyId;

            // Check if invoice already exists
            $existingInvoice = PurchaseInvoice::where('company_id', $this->companyId)
                ->where('number', $invoiceData['number'])
                ->first();

            if ($existingInvoice) {
                $existingInvoice->update($invoiceData);
                $this->importResults['updated']++;
                $this->importResults['details'][] = "Updated invoice: {$invoiceData['number']} (row $rowNumber)";
            } else {
                $invoice = PurchaseInvoice::create($invoiceData);
                $this->importResults['imported']++;
                $this->importResults['details'][] = "Imported invoice: {$invoiceData['number']} (row $rowNumber)";
            }
        } catch (\Exception $e) {
            $this->importResults['errors']++;
            $errorDetails = "Error processing row $rowNumber: " . $e->getMessage();
            $this->importResults['details'][] = $errorDetails;
            Log::error('Purchase invoice import error', [
                'row_number' => $rowNumber,
                'row' => $row,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function mapRowToInvoiceData(array $row): array
    {
        return [
            'number' => $this->cleanString($row['nr_'] ?? $row['nr'] ?? null),
            'supplier_invoice_number' => $this->cleanString($row['nr__fatt__fornitore'] ?? null),
            'supplier_number' => $this->cleanString($row['nr__fornitore'] ?? null),
            'supplier' => $this->cleanString($row['fornitore'] ?? null),
            'currency_code' => $this->cleanString($row['cod__valuta'] ?? null),
            'amount' => $this->parseDecimal($row['importo'] ?? null),
            'amount_including_vat' => $this->parseDecimal($row['importo_iva_inclusa'] ?? null),
            'pay_to_cap' => $this->cleanString($row['pagare_a___cap'] ?? null),
            'pay_to_country_code' => $this->cleanString($row['pagare_a___cod__paese'] ?? null),
            'registration_date' => $this->parseDate($row['data_di_registrazione'] ?? null),
            'location_code' => $this->cleanString($row['cod__ubicazione'] ?? null),
            'printed_copies' => $this->parseInteger($row['copie_stampate'] ?? 0),
            'document_date' => $this->parseDate($row['data_documento'] ?? null),
            'payment_condition_code' => $this->cleanString($row['cod__condizioni_pagam_'] ?? null),
            'due_date' => $this->parseDate($row['data_scadenza'] ?? null),
            'payment_method_code' => $this->cleanString($row['cod__metodo_di_pagamento'] ?? null),
            'residual_amount' => $this->parseDecimal($row['importo_residuo'] ?? null),
            'closed' => $this->parseBoolean($row['chiuso'] ?? null),
            'cancelled' => $this->parseBoolean($row['annullato'] ?? null),
            'corrected' => $this->parseBoolean($row['rettifica'] ?? null),
            'pay_to_address' => $this->cleanString($row['pagare_a___indirizzo'] ?? null),
            'pay_to_city' => $this->cleanString($row['pagare_a___città'] ?? null),
            'supplier_category' => $this->cleanString($row['cat__reg__fornitore'] ?? null),
            'exchange_rate' => $this->parseDecimal($row['fattore_valuta'] ?? null),
            'vat_number' => $this->cleanString($row['partita_iva'] ?? null),
            'fiscal_code' => $this->cleanString($row['codice_fiscale'] ?? null),
            'document_type' => $this->cleanString($row['tipo_documento_fattura'] ?? null),
        ];
    }

    protected function cleanString($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return trim(preg_replace('/\s+/', ' ', $value));
    }

    protected function parseDecimal($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Remove dots and replace comma with dot for Italian decimal format
        $cleaned = str_replace('.', '', $value);
        $cleaned = str_replace(',', '.', $cleaned);

        if (is_numeric($cleaned)) {
            return (float) $cleaned;
        }

        return null;
    }

    protected function parseInteger($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $cleaned = preg_replace('/[^0-9]/', '', $value);

        if (is_numeric($cleaned)) {
            return (int) $cleaned;
        }

        return 0;
    }

    protected function parseDate($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            // Try Italian date format d/m/Y first
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                return \Carbon\Carbon::createFromFormat('d/m/Y', $value);
            }

            // Try other common formats
            $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d', 'Y-m-d H:i:s'];

            foreach ($formats as $format) {
                try {
                    return \Carbon\Carbon::createFromFormat($format, $value);
                } catch (\Exception $e) {
                    continue;
                }
            }

            // If no format works, try Carbon's flexible parsing
            return new \Carbon\Carbon($value);
        } catch (\Exception $e) {
            Log::warning('Failed to parse date: ' . $value);
            return null;
        }
    }

    protected function parseBoolean($value)
    {
        if ($value === null || $value === '') {
            return false;
        }

        // Italian boolean values
        $trueValues = ['VERO', 'TRUE', '1', 'SI', 'SÌ', 'YES'];
        $falseValues = ['FALSO', 'FALSE', '0', 'NO'];

        $upperValue = strtoupper(trim($value));

        if (in_array($upperValue, $trueValues)) {
            return true;
        }

        if (in_array($upperValue, $falseValues)) {
            return false;
        }

        return false;
    }

    /**
     * Match purchase invoices to agents by VAT number
     * Updates the polymorphic relationship for matching invoices
     */
    protected function matchAgentsByVatNumber(): void
    {
        $matchedCount = 0;

        try {
            // Get all purchase invoices for this company that have a VAT number but no invoiceable relationship
            $invoices = PurchaseInvoice::where('company_id', $this->companyId)
                ->whereNotNull('vat_number')
                ->where(function ($query) {
                    $query
                        ->whereNull('invoiceable_type')
                        ->orWhereNull('invoiceable_id');
                })
                ->get();

            Log::info('Starting agent matching by VAT number', [
                'company_id' => $this->companyId,
                'invoices_to_check' => $invoices->count()
            ]);

            foreach ($invoices as $invoice) {
                // Clean VAT number for comparison
                $cleanVatNumber = $this->cleanVatNumber($invoice->vat_number);

                if (empty($cleanVatNumber)) {
                    continue;
                }

                // Find agent with matching VAT number
                $agent = $this->findAgentByVatNumber($cleanVatNumber);

                if ($agent) {
                    // Update the invoice with the agent relationship
                    $invoice->update([
                        'invoiceable_type' => Agent::class,
                        'invoiceable_id' => $agent->id,
                    ]);

                    $matchedCount++;

                    Log::info('Matched invoice to agent', [
                        'invoice_number' => $invoice->number,
                        'invoice_vat' => $invoice->vat_number,
                        'agent_id' => $agent->id,
                        'agent_name' => $agent->name,
                        'agent_vat' => $agent->vat_number,
                    ]);
                }
            }

            Log::info('agent matching completed', [
                'company_id' => $this->companyId,
                'total_checked' => $invoices->count(),
                'matched' => $matchedCount,
            ]);

            // Update import results
            $this->importResults['agent_matches'] = $matchedCount;
            $this->importResults['details'][] = "Matched {$matchedCount} invoices to agents by VAT number";
        } catch (\Exception $e) {
            Log::error('agent matching failed', [
                'company_id' => $this->companyId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->importResults['agent_match_errors'] = ($this->importResults['agent_match_errors'] ?? 0) + 1;
            $this->importResults['details'][] = 'agent matching error: ' . $e->getMessage();
        }
    }

    /**
     * Find agent by VAT number with flexible matching
     */
    protected function findAgentByVatNumber(string $vatNumber): ?Agent
    {
        // Try exact match first
        $agent = Agent::where('vat_number', $vatNumber)
            ->where('company_id', $this->companyId)
            ->first();

        if ($agent) {
            return $agent;
        }

        // Try cleaned versions
        $cleanedVariations = $this->getVatNumberVariations($vatNumber);

        foreach ($cleanedVariations as $variation) {
            $agent = Agent::where('vat_number', $variation)
                ->where('company_id', $this->companyId)
                ->first();

            if ($agent) {
                return $agent;
            }
        }

        return null;
    }

    /**
     * Clean and normalize VAT number for comparison
     */
    protected function cleanVatNumber(string $vatNumber): string
    {
        // Remove spaces, dots, dashes, and common Italian VAT formatting
        $cleaned = preg_replace('/[\s\.\-_]/', '', $vatNumber);

        // Remove country prefix if present (IT for Italy)
        if (str_starts_with(strtoupper($cleaned), 'IT')) {
            $cleaned = substr($cleaned, 2);
        }

        // Remove any remaining non-alphanumeric characters
        $cleaned = preg_replace('/[^A-Z0-9]/', '', strtoupper($cleaned));

        return $cleaned;
    }

    /**
     * Get variations of VAT number for flexible matching
     */
    protected function getVatNumberVariations(string $vatNumber): array
    {
        $variations = [$vatNumber];

        // Add with country prefix
        if (!str_starts_with(strtoupper($vatNumber), 'IT')) {
            $variations[] = 'IT' . $vatNumber;
        }

        // Add with spaces and formatting variations
        $formatted = preg_replace('/([A-Z0-9]{2})/', '$1 ', $vatNumber);
        $formatted = trim($formatted);
        if ($formatted !== $vatNumber) {
            $variations[] = $formatted;
        }

        return array_unique($variations);
    }

    /**
     * Match purchase invoices to clients by VAT number
     * Updates the polymorphic relationship for matching invoices
     */
    protected function matchClientsByVatNumber(): void
    {
        $matchedCount = 0;

        try {
            // Get all purchase invoices for this company that have a VAT number but no invoiceable relationship
            // or that weren't matched to agents
            $invoices = PurchaseInvoice::where('company_id', $this->companyId)
                ->whereNotNull('vat_number')
                ->where(function ($query) {
                    $query
                        ->whereNull('invoiceable_type')
                        ->orWhereNull('invoiceable_id')
                        ->orWhere(function ($subQuery) {
                            // Also check invoices that might have been matched to agent but could be clients
                            $subQuery->where('invoiceable_type', '!=', Agent::class);
                        });
                })
                ->get();

            Log::info('Starting client matching by VAT number', [
                'company_id' => $this->companyId,
                'invoices_to_check' => $invoices->count()
            ]);

            foreach ($invoices as $invoice) {
                // Skip if already matched to agent
                if ($invoice->invoiceable_type === Agent::class && $invoice->invoiceable_id) {
                    continue;
                }

                // Clean VAT number for comparison
                $cleanVatNumber = $this->cleanVatNumber($invoice->vat_number);

                if (empty($cleanVatNumber)) {
                    continue;
                }

                // Find client with matching VAT number
                $client = $this->findClientByVatNumber($cleanVatNumber);

                if ($client) {
                    // Update the invoice with the client relationship
                    $invoice->update([
                        'invoiceable_type' => Client::class,
                        'invoiceable_id' => $client->id,
                    ]);

                    $matchedCount++;

                    Log::info('Matched invoice to client by VAT number', [
                        'invoice_number' => $invoice->number,
                        'invoice_vat' => $invoice->vat_number,
                        'is_person' => length($invoice->vat_number) > 13,
                        'client_id' => $client->id,
                        'client_name' => $client->name,
                        'client_tax_code' => $client->tax_code,
                    ]);
                } else {
                    // Try to match by supplier name similarity
                    $client = $this->findClientByNameSimilarity($invoice->supplier);

                    if ($client) {
                        // Update the invoice with the client relationship
                        $invoice->update([
                            'invoiceable_type' => Client::class,
                            'invoiceable_id' => $client->id,
                        ]);

                        $matchedCount++;

                        Log::info('Matched invoice to client by name similarity', [
                            'invoice_number' => $invoice->number,
                            'invoice_supplier' => $invoice->supplier,
                            'client_id' => $client->id,
                            'client_name' => $client->name,
                            'similarity_score' => $this->calculateSimilarity($invoice->supplier, $client->name),
                        ]);
                    } else {
                        // Create new client if no matches found
                        $newClient = $this->createClientFromInvoice($invoice);

                        if ($newClient) {
                            // Update the invoice with the new client relationship
                            $invoice->update([
                                'invoiceable_type' => Client::class,
                                'invoiceable_id' => $newClient->id,
                            ]);

                            $this->importResults['clients_created'] = ($this->importResults['clients_created'] ?? 0) + 1;

                            Log::info('Created new client from invoice', [
                                'invoice_number' => $invoice->number,
                                'invoice_supplier' => $invoice->supplier,
                                'invoice_vat' => $invoice->vat_number,
                                'new_client_id' => $newClient->id,
                                'new_client_name' => $newClient->name,
                            ]);
                        }
                    }
                }
            }

            Log::info('Client matching completed', [
                'company_id' => $this->companyId,
                'total_checked' => $invoices->count(),
                'matched' => $matchedCount,
            ]);

            // Update import results
            $this->importResults['client_matches'] = $matchedCount;
            $this->importResults['details'][] = "Matched {$matchedCount} invoices to clients by VAT number";
        } catch (\Exception $e) {
            Log::error('Client matching failed', [
                'company_id' => $this->companyId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->importResults['client_match_errors'] = ($this->importResults['client_match_errors'] ?? 0) + 1;
            $this->importResults['details'][] = 'Client matching error: ' . $e->getMessage();
        }
    }

    /**
     * Find client by VAT number with flexible matching
     */
    protected function findClientByVatNumber(string $vatNumber): ?Client
    {
        // Try exact match first on tax_code (which often contains VAT number for companies)
        $client = Client::where('tax_code', $vatNumber)
            ->where('company_id', $this->companyId)
            ->first();

        if ($client) {
            return $client;
        }

        // Try cleaned versions
        $cleanedVariations = $this->getVatNumberVariations($vatNumber);

        foreach ($cleanedVariations as $variation) {
            $client = Client::where('tax_code', $variation)
                ->where('company_id', $this->companyId)
                ->first();

            if ($client) {
                return $client;
            }
        }

        // Also try to match by VAT number if clients have that field
        // (Some implementations might store VAT in a separate field)
        $client = Client::where('vat_number', $vatNumber)
            ->where('company_id', $this->companyId)
            ->first();

        if ($client) {
            return $client;
        }

        foreach ($cleanedVariations as $variation) {
            $client = Client::where('vat_number', $variation)
                ->where('company_id', $this->companyId)
                ->first();

            if ($client) {
                return $client;
            }
        }

        return null;
    }

    /**
     * Find client by name similarity using fuzzy matching
     */
    protected function findClientByNameSimilarity(string $supplierName): ?Client
    {
        if (empty($supplierName)) {
            return null;
        }

        // Get all clients for this company
        $clients = Client::where('company_id', $this->companyId)
            ->whereNotNull('name')
            ->get();

        $bestMatch = null;
        $bestScore = 0;
        $similarityThreshold = 70;  // 70% similarity threshold

        foreach ($clients as $client) {
            $score = $this->calculateSimilarity($supplierName, $client->name);

            if ($score > $bestScore && $score >= $similarityThreshold) {
                $bestScore = $score;
                $bestMatch = $client;
            }
        }

        return $bestMatch;
    }

    /**
     * Calculate similarity between two strings using Levenshtein distance
     */
    protected function calculateSimilarity(string $string1, string $string2): int
    {
        $string1 = strtolower(trim($string1));
        $string2 = strtolower(trim($string2));

        if (empty($string1) || empty($string2)) {
            return 0;
        }

        // Remove common company suffixes for better matching
        $suffixes = ['s.r.l.', 'srl', 's.p.a.', 'spa', 'ltd', 'limited', 'inc', 'llc', 'gmbh'];
        foreach ($suffixes as $suffix) {
            $string1 = preg_replace('/\b' . preg_quote($suffix) . '\b/i', '', $string1);
            $string2 = preg_replace('/\b' . preg_quote($suffix) . '\b/i', '', $string2);
        }

        // Clean up extra spaces
        $string1 = preg_replace('/\s+/', ' ', trim($string1));
        $string2 = preg_replace('/\s+/', ' ', trim($string2));

        // Use Levenshtein distance for similarity calculation
        $distance = levenshtein($string1, $string2);
        $maxLength = max(strlen($string1), strlen($string2));

        if ($maxLength === 0) {
            return 100;
        }

        $similarity = 100 - (($distance / $maxLength) * 100);

        return (int) round($similarity);
    }

    /**
     * Create a new client from invoice data
     */
    protected function createClientFromInvoice(PurchaseInvoice $invoice): ?Client
    {
        try {
            $clientData = [
                'company_id' => $this->companyId,
                'name' => $invoice->supplier,
                'tax_code' => $invoice->vat_number,
                'is_client' => true,
                'is_company' => true,  // Assume suppliers are companies
                'status' => 'active',
            ];

            // Try to extract additional information from invoice
            if ($invoice->pay_to_address) {
                $clientData['address'] = $invoice->pay_to_address;
            }

            if ($invoice->pay_to_city) {
                $clientData['city'] = $invoice->pay_to_city;
            }

            if ($invoice->pay_to_cap) {
                $clientData['postal_code'] = $invoice->pay_to_cap;
            }

            $client = Client::create($clientData);

            Log::info('Successfully created new client', [
                'client_id' => $client->id,
                'client_name' => $client->name,
                'invoice_number' => $invoice->number,
                'tax_code' => $client->tax_code,
            ]);

            return $client;
        } catch (\Exception $e) {
            Log::error('Failed to create client from invoice', [
                'invoice_number' => $invoice->number,
                'supplier_name' => $invoice->supplier,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
