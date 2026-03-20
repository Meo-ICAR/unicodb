<?php

namespace App\Console\Commands;

use App\Models\Principal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportOamPrincipalsCommand extends Command
{
    protected $signature = "principals:import-oam
                            {--file=BANCHE E PRODOTTI - Elenco per l'OAM.csv : Path to OAM CSV file}
                            {--dry-run : Show what would be imported without actually importing}
                            {--update-only : Only update existing principals, don't create new ones}";

    protected $description = 'Import or update principals from OAM CSV file with VAT number updates';

    public function handle()
    {
        $filePath = $this->option('file');
        $isDryRun = $this->option('dry-run');
        $updateOnly = $this->option('update-only');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("Processing OAM file: {$filePath}");
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No data will be imported');
        }
        if ($updateOnly) {
            $this->warn('UPDATE ONLY MODE - Only updating existing principals');
        }

        $csvData = $this->readCsv($filePath);
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($csvData as $index => $row) {
            try {
                $result = $this->processRow($row, $index + 2, $isDryRun, $updateOnly);

                if ($result === 'created') {
                    $created++;
                } elseif ($result === 'updated') {
                    $updated++;
                } elseif ($result === 'skipped') {
                    $skipped++;
                } else {
                    $errors++;
                }
            } catch (\Exception $e) {
                $this->error('Error processing row ' . ($index + 2) . ': ' . $e->getMessage());
                $errors++;
            }
        }

        $this->newLine();
        $this->info('Import Summary:');
        $this->line("✓ Created: {$created} new principals");
        $this->line("✓ Updated: {$updated} existing principals");
        $this->line("- Skipped: {$skipped} principals (no changes needed)");
        $this->line("✗ Errors: {$errors} rows");

        if (!$isDryRun && ($created > 0 || $updated > 0)) {
            $this->info("Successfully processed {$created} new and {$updated} updated principals!");
        }

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function readCsv($filePath): array
    {
        $data = [];
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw new \Exception("Unable to open file: {$filePath}");
        }

        // Skip header row
        fgetcsv($handle, 1000, ';');

        while (($row = fgetcsv($handle, 1000, ';')) !== false) {
            // Skip empty rows
            if (empty($row[0]) || empty(trim($row[0]))) {
                continue;
            }
            $data[] = $row;
        }

        fclose($handle);
        return $data;
    }

    private function processRow(array $row, int $rowNumber, bool $isDryRun, bool $updateOnly): string
    {
        // Extract data from CSV columns
        $bankName = trim($row[0] ?? '');
        $vatNumber = trim($row[1] ?? '');
        $stipulatedAt = trim($row[2] ?? '');
        $duration = trim($row[3] ?? '');
        $renewalType = trim($row[4] ?? '');
        $rescissionSent = trim($row[5] ?? '');
        $rescissionEffective = trim($row[6] ?? '');
        $currentStatus = trim($row[7] ?? '');
        $statusOnDate = trim($row[8] ?? '');
        $cessioni = trim($row[9] ?? '');
        $prestitiPersonali = trim($row[10] ?? '');
        $tfs = trim($row[11] ?? '');
        $mutui = trim($row[12] ?? '');
        $totalProducts = trim($row[13] ?? '');

        // Skip if no bank name
        if (empty($bankName)) {
            return 'skipped';
        }

        // Clean bank name (remove extra spaces and special characters)
        $bankName = preg_replace('/\s+/', ' ', $bankName);
        $bankName = trim($bankName, " \t\n\r\0\v.");

        // Try to find existing principal by name (fuzzy matching)
        $existingPrincipal = $this->findExistingPrincipal($bankName);

        if ($existingPrincipal) {
            // Check if VAT number needs updating
            $needsUpdate = false;
            $updateData = [];

            if (!empty($vatNumber) && $existingPrincipal->vat_number !== $vatNumber) {
                $updateData['vat_number'] = $vatNumber;
                $needsUpdate = true;
            }

            // Update other fields if they're missing
            if (empty($existingPrincipal->stipulated_at) && !empty($stipulatedAt)) {
                $updateData['stipulated_at'] = $this->parseDate($stipulatedAt);
                $needsUpdate = true;
            }

            if (empty($existingPrincipal->dismissed_at) && !empty($rescissionEffective)) {
                $updateData['dismissed_at'] = $this->parseDate($rescissionEffective);
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                if (!$isDryRun) {
                    $existingPrincipal->update($updateData);
                    $this->info("✓ Updated existing principal: {$bankName}");
                    if (isset($updateData['vat_number'])) {
                        $this->line("  VAT: {$existingPrincipal->vat_number} → {$updateData['vat_number']}");
                    }
                } else {
                    $this->info("Would update existing principal: {$bankName}");
                    if (isset($updateData['vat_number'])) {
                        $this->line("  VAT: {$existingPrincipal->vat_number} → {$updateData['vat_number']}");
                    }
                }
                return 'updated';
            } else {
                $this->line("- Principal up to date: {$bankName}");
                return 'skipped';
            }
        }

        // If update-only mode, skip creating new principals
        if ($updateOnly) {
            $this->line("- Principal not found (update-only mode): {$bankName}");
            return 'skipped';
        }

        // Parse dates
        $stipulatedAtDate = $this->parseDate($stipulatedAt);
        $rescissionEffectiveDate = $this->parseDate($rescissionEffective);

        // Determine principal type and status
        $isActive = strtolower($currentStatus) === 'attivo';
        $isDummy = false;

        // Map CSV status to database enum values
        $status = 'ATTIVO';
        if (strtolower($currentStatus) === 'cessato' || strtolower($currentStatus) === 'scaduto') {
            $status = 'SCADUTO';
        } elseif (strtolower($currentStatus) === 'receduto') {
            $status = 'RECEDUTO';
        } elseif (strtolower($currentStatus) === 'sospeso') {
            $status = 'SOPESO';
        }

        if ($isDryRun) {
            $this->info("Would create new principal: {$bankName}");
            return 'created';
        }

        // Create new principal
        $principal = Principal::create([
            'name' => $bankName,
            'vat_number' => $vatNumber ?: null,
            'stipulated_at' => $stipulatedAtDate,
            'dismissed_at' => $rescissionEffectiveDate,
            'is_active' => $isActive,
            'is_dummy' => $isDummy,
            'status' => $status,
            'type' => $this->detectPrincipalType($bankName),
            'principal_type' => 'banca',
            'submission_type' => 'accesso portale',
            'notes' => $this->generateOamNotes($duration, $renewalType, $rescissionSent, $currentStatus, $statusOnDate, $cessioni, $prestitiPersonali, $tfs, $mutui, $totalProducts),
        ]);

        $this->info("✓ Created new principal: {$bankName} (VAT: {$vatNumber})");
        return 'created';
    }

    private function findExistingPrincipal(string $bankName): ?Principal
    {
        // First try exact match
        $principal = Principal::where('name', $bankName)->first();
        if ($principal) {
            return $principal;
        }

        // Try fuzzy matching - remove common suffixes/prefixes
        $searchName = $bankName;
        $searchName = preg_replace('/\s+(S\.P\.A\.|S\.R\.L\.|S\.A\.|SPA|SRL|SA)$/i', '', $searchName);
        $searchName = preg_replace('/^(BANCA|BANCA\s+)/i', '', $searchName);
        $searchName = trim($searchName);

        // Try partial match
        $principal = Principal::where('name', 'LIKE', "%{$searchName}%")->first();
        if ($principal) {
            return $principal;
        }

        // Try matching key words
        $keywords = explode(' ', $searchName);
        if (count($keywords) >= 2) {
            $principal = Principal::where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    if (strlen($keyword) > 3) {
                        $query->orWhere('name', 'LIKE', "%{$keyword}%");
                    }
                }
            })->first();
            if ($principal) {
                return $principal;
            }
        }

        return null;
    }

    private function parseDate(string $dateString): ?\Carbon\Carbon
    {
        if (empty($dateString) || $dateString === 'Nessuno') {
            return null;
        }

        try {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $dateString);
        } catch (\Exception $e) {
            Log::warning("Could not parse date: {$dateString}");
            return null;
        }
    }

    private function detectPrincipalType(string $bankName): string
    {
        $name = strtolower($bankName);

        if (str_contains($name, 'assicurazione') || str_contains($name, 'insurance')) {
            return 'Assicurazione';
        } elseif (str_contains($name, 'finanziaria') || str_contains($name, 'finance') || str_contains($name, 'credit')) {
            return 'Finanziaria';
        } elseif (str_contains($name, 'broker')) {
            return 'Broker';
        } elseif (str_contains($name, 'utility')) {
            return 'Utility';
        } else {
            return 'Banca';
        }
    }

    private function generateOamNotes(string $duration, string $renewalType, string $rescissionSent, string $currentStatus, string $statusOnDate, string $cessioni, string $prestitiPersonali, string $tfs, string $mutui, string $totalProducts): string
    {
        $notes = [];

        if (!empty($duration)) {
            $notes[] = "Durata: {$duration}";
        }

        if (!empty($renewalType) && $renewalType !== 'Nessuno') {
            $notes[] = "Rinnovo: {$renewalType}";
        }

        if (!empty($rescissionSent) && $rescissionSent !== 'Nessuno') {
            $notes[] = "Invio recesso: {$rescissionSent}";
        }

        if (!empty($currentStatus)) {
            $notes[] = "Stato attuale: {$currentStatus}";
        }

        // Product types
        $products = [];
        if ($cessioni === 'SI')
            $products[] = 'Cessioni';
        if ($prestitiPersonali === 'SI')
            $products[] = 'Prestiti Personali';
        if ($tfs === 'SI')
            $products[] = 'TFS';
        if ($mutui === 'SI')
            $products[] = 'Mutui';

        if (!empty($products)) {
            $notes[] = 'Prodotti: ' . implode(', ', $products);
        }

        if (!empty($totalProducts)) {
            $notes[] = "Descrizione: {$totalProducts}";
        }

        return implode(' | ', $notes);
    }
}
