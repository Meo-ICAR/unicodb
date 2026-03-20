<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Client;
use App\Models\Principal;
use App\Models\SalesInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesInvoiceImportService
{
    protected $companyId;
    protected $filename;

    protected $importResults = [
        'imported' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => 0,
        'details' => []
    ];

    public function __construct($filename = null)
    {
        $this->filename = $filename;
    }

    public function import($filePath, $companyId)
    {
        $this->companyId = $companyId;

        // Extract filename from path if not provided
        if (!$this->filename) {
            $this->filename = basename($filePath);
        }

        $this->importResults = [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
            'details' => [],
            'filename' => $this->filename,
        ];

        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        DB::beginTransaction();

        try {
            $handle = fopen($filePath, 'r');
            if (!$handle) {
                throw new \Exception("Cannot open file: {$filePath}");
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
                $cleanHeader = str_replace(['.', ' ', '-', '(', ')', '/', '°'], ['_', '_', '_', '_', '_', '_', '_'], $cleanHeader);
                $cleanHeader = strtolower($cleanHeader);
                $cleanHeaders[] = $cleanHeader;
            }

            Log::info('Sales Invoice CSV Headers', ['original' => $headers, 'cleaned' => $cleanHeaders]);

            $rowNumber = 2;  // Start from 2 since we already read header

            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                $this->processRow($row, $cleanHeaders, $rowNumber);
                $rowNumber++;
            }

            fclose($handle);

            DB::commit();

            Log::info('Sales invoices import completed', [
                'file' => $filePath,
                'company_id' => $this->companyId,
                'results' => $this->importResults
            ]);
            DB::UPDATE("
            UPDATE principals b
JOIN (
    -- This subquery identifies the specific principals and the new VAT values
    SELECT p.principal_id, s.vat_number
    FROM practice_commissions p
    INNER JOIN sales_invoices s ON s.registration_date = p.invoice_at
    WHERE YEAR(p.invoice_at) > 2024
      AND p.tipo = 'Istituto'
    GROUP BY p.principal_id, p.invoice_at, p.invoice_number, s.amount, s.vat_number
    HAVING s.amount = SUM(p.amount)
) src ON b.id = src.principal_id
SET b.vat_number = src.vat_number;
");
            DB::UPDATE("
UPDATE practice_commissions p
INNER JOIN (
    -- Subquery to find the valid matches based on your totals
    SELECT
        p_inner.principal_id,
        p_inner.invoice_at,
        s.number AS invoice_ref_number
    FROM practice_commissions p_inner
    INNER JOIN principals b ON b.id = p_inner.principal_id
    INNER JOIN sales_invoices s ON s.vat_number = b.vat_number
    WHERE p_inner.tipo = 'Istituto'
      AND YEAR(p_inner.invoice_at) = 2025
and p_inner.alternative_number_invoice is null
    GROUP BY b.id, b.name, b.vat_number, p_inner.invoice_at, s.registration_date, s.number
    HAVING ABS(SUM(p_inner.amount) - SUM(s.amount)) < 100
) AS matched_data ON p.principal_id = matched_data.principal_id
                 AND p.invoice_at = matched_data.invoice_at
SET p.alternative_number_invoice = matched_data.invoice_ref_number
WHERE p.tipo = 'Istituto'; ");
            return $this->importResults;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function processRow(array $row, array $headers, int $rowNumber)
    {
        try {
            // Use direct index mapping instead of array_combine for reliability
            $rowData = [];
            foreach ($headers as $index => $header) {
                $rowData[$header] = $row[$index] ?? '';
            }

            // Skip empty rows
            if (empty($rowData['nr_']) || empty($rowData['ragione_sociale'])) {
                $this->importResults['skipped']++;
                return;
            }

            $invoiceData = $this->mapRowToInvoiceData($rowData);

            // Add company_id
            $invoiceData['company_id'] = $this->companyId;

            // Check if invoice already exists
            $existingInvoice = SalesInvoice::where('company_id', $this->companyId)
                ->where('number', $invoiceData['number'])
                ->first();

            if ($existingInvoice) {
                $existingInvoice->update($invoiceData);
                $this->importResults['updated']++;
                $this->importResults['details'][] = "Updated invoice: {$invoiceData['number']} (row $rowNumber)";
            } else {
                $invoice = SalesInvoice::create($invoiceData);
                $this->importResults['imported']++;
                $this->importResults['details'][] = "Imported invoice: {$invoiceData['number']} (row $rowNumber)";
            }
        } catch (\Exception $e) {
            $this->importResults['errors']++;
            $errorDetails = "Error processing row $rowNumber: " . $e->getMessage();
            $this->importResults['details'][] = $errorDetails;
            Log::error('Sales invoice import error', [
                'row_number' => $rowNumber,
                'row' => $row,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function mapRowToInvoiceData(array $row): array
    {
        return [
            'number' => $this->cleanString($row['nr_'] ?? null),
            'order_number' => $this->cleanString($row['nr__ordine'] ?? null),
            'customer_number' => $this->cleanString($row['nr__cliente'] ?? null),
            'customer_name' => $this->cleanString($row['ragione_sociale'] ?? null),
            'currency_code' => $this->cleanString($row['cod__valuta'] ?? null),
            'due_date' => $this->parseDate($row['data_scadenza'] ?? null),
            'amount' => $this->parseDecimal($row['importo'] ?? null),
            'amount_including_vat' => $this->parseDecimal($row['importo_iva_inclusa'] ?? null),
            'residual_amount' => $this->parseDecimal($row['importo_residuo'] ?? null),
            'ship_to_code' => $this->cleanString($row['spedire_a___codice'] ?? null),
            'ship_to_cap' => $this->cleanString($row['spedire_a___cap'] ?? null),
            'registration_date' => $this->parseDate($row['data_di_registrazione'] ?? null),
            'agent_code' => $this->cleanString($row['cod__agente'] ?? null),
            'cdc_code' => $this->cleanString($row['cdc_codice'] ?? null),
            'dimensional_link_code' => $this->cleanString($row['cod__colleg__dimen__2'] ?? null),
            'location_code' => $this->cleanString($row['cod__ubicazione'] ?? null),
            'printed_copies' => $this->parseInteger($row['copie_stampate'] ?? 0),
            'payment_condition_code' => $this->cleanString($row['cod__condizioni_pagam_'] ?? null),
            'closed' => $this->parseBoolean($row['chiuso'] ?? null),
            'cancelled' => $this->parseBoolean($row['annullato'] ?? null),
            'corrected' => $this->parseBoolean($row['rettifica'] ?? null),
            'email_sent' => $this->parseBoolean($row['e_mail_inviata'] ?? null),
            'email_sent_at' => $this->parseDateTime($row['data__ora_invio_mail'] ?? null),
            'bill_to_address' => $this->cleanString($row['fatturare_a___indirizzo'] ?? null),
            'bill_to_city' => $this->cleanString($row['fatturare_a___città'] ?? null),
            'bill_to_province' => $this->cleanString($row['provincia_di_fatturazione'] ?? null),
            'ship_to_address' => $this->cleanString($row['spedire_a___indirizzo'] ?? null),
            'ship_to_city' => $this->cleanString($row['spedire_a___città'] ?? null),
            'payment_method_code' => $this->cleanString($row['cod__metodo_di_pagamento'] ?? null),
            'customer_category' => $this->cleanString($row['cat__reg__cliente'] ?? null),
            'exchange_rate' => $this->parseDecimal($row['fattore_valuta'] ?? null),
            'vat_number' => $this->cleanString($row['partita_iva'] ?? null),
            'bank_account' => $this->cleanString($row['c_c_bancario'] ?? null),
            'document_residual_amount' => $this->parseDecimal($row['importo_residuo_documento'] ?? null),
            'document_type' => $this->cleanString($row['tipo_di_documento_fattura'] ?? null),
            'credit_note_linked' => $this->cleanString($row['nota_di_credito_collegata'] ?? null),
            'in_order' => $this->parseBoolean($row['flg_in_commessa'] ?? null),
            'supplier_number' => $this->cleanString($row['nr__fornitore'] ?? null),
            'supplier_description' => $this->cleanString($row['descrizione_fornitore'] ?? null),
            'purchase_invoice_origin' => $this->cleanString($row['fattura_acquisto_origine'] ?? null),
            'sent_to_sdi' => $this->parseBoolean($row['inviato_allo_sdi'] ?? null),
        ];
    }

    protected function cleanString($value)
    {
        if (empty($value)) {
            return null;
        }
        return trim($value);
    }

    protected function parseDecimal($value)
    {
        if (empty($value)) {
            return 0;
        }

        // Handle Italian format: 15.000,00 -> 15000.00
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return (float) $value;
    }

    protected function parseInteger($value)
    {
        if (empty($value)) {
            return 0;
        }

        return (int) $value;
    }

    protected function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // Handle Italian format: 30/12/2025 -> 2025-12-30
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $value, $matches)) {
            return "{$matches[3]}-{$matches[2]}-{$matches[1]}";
        }

        return null;
    }

    protected function parseDateTime($value)
    {
        if (empty($value)) {
            return null;
        }

        // Try to parse various datetime formats
        try {
            return \Carbon\Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function parseBoolean($value)
    {
        if (empty($value)) {
            return false;
        }

        return in_array(strtolower($value), ['vero', 'true', '1', 'si', 'yes']);
    }
}
