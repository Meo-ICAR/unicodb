<?php

namespace App\Console\Commands;

use App\Models\Principal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportReportingPrincipalsCommand extends Command
{
    protected $signature = 'principals:import-reporting
                            {--file=BANCHE E PRODOTTI - segnalazione.csv : Path to CSV file}
                            {--dry-run : Show what would be imported without actually importing}';

    protected $description = 'Import missing principals from reporting CSV file with is_reported=true';

    public function handle()
    {
        $filePath = $this->option('file');
        $isDryRun = $this->option('dry-run');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("Processing file: {$filePath}");
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No data will be imported');
        }

        $csvData = $this->readCsv($filePath);
        $imported = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($csvData as $index => $row) {
            try {
                $result = $this->processRow($row, $index + 2, $isDryRun);

                if ($result === 'imported') {
                    $imported++;
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
        $this->line("✓ Imported: {$imported} principals");
        $this->line("- Skipped: {$skipped} principals (already exist)");
        $this->line("✗ Errors: {$errors} rows");

        if (!$isDryRun && $imported > 0) {
            $this->info("Successfully imported {$imported} new principals with reporting agreements!");
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

    private function processRow(array $row, int $rowNumber, bool $isDryRun): string
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

        // Skip if no bank name
        if (empty($bankName)) {
            return 'skipped';
        }

        // Check if principal already exists (by name or VAT number)
        $existingPrincipal = null;

        if (!empty($vatNumber)) {
            $existingPrincipal = Principal::where('vat_number', $vatNumber)->first();
        }

        if (!$existingPrincipal && !empty($bankName)) {
            $existingPrincipal = Principal::where('name', $bankName)->first();
        }

        if ($existingPrincipal) {
            // Update existing principal if it doesn't have reporting flag set
            if (!$existingPrincipal->is_reported) {
                if (!$isDryRun) {
                    $existingPrincipal->update(['is_reported' => true]);
                    $this->info("✓ Updated existing principal: {$bankName} (set is_reported=true)");
                } else {
                    $this->info("Would update existing principal: {$bankName} (set is_reported=true)");
                }
                return 'imported';
            } else {
                $this->line("- Principal already exists with reporting: {$bankName}");
                return 'skipped';
            }
        }

        // Parse dates
        $stipulatedAtDate = $this->parseDate($stipulatedAt);
        $rescissionEffectiveDate = $this->parseDate($rescissionEffective);

        // Determine principal type and status
        $isActive = strtolower($currentStatus) === 'attivo';
        $isDummy = false;  // These are real reporting principals

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
            return 'imported';
        }

        // Create new principal
        $principal = Principal::create([
            'name' => $bankName,
            'vat_number' => $vatNumber ?: null,
            'stipulated_at' => $stipulatedAtDate,
            'dismissed_at' => $rescissionEffectiveDate,
            'is_active' => $isActive,
            'is_reported' => true,
            'is_dummy' => $isDummy,
            'status' => $status,
            'type' => $this->detectPrincipalType($bankName),
            'principal_type' => 'banca',
            'submission_type' => 'accesso portale',
            'notes' => $this->generateNotes($duration, $renewalType, $rescissionSent, $currentStatus, $statusOnDate),
        ]);

        $this->info("✓ Created new principal: {$bankName} (VAT: {$vatNumber})");
        return 'imported';
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

    private function generateNotes(string $duration, string $renewalType, string $rescissionSent, string $currentStatus, string $statusOnDate): string
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

        if (!empty($statusOnDate)) {
            $notes[] = "Stato al 01/07/2025: {$statusOnDate}";
        }

        return implode(' | ', $notes);
    }
}
