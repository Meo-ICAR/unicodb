<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportOamStaffCommand extends Command
{
    protected $signature = 'staff:import-oam
                            {--file=Monitoraggio Numero DIP. COLLAB. su OAM.csv : Path to OAM staff CSV file}
                            {--dry-run : Show what would be imported without actually importing}';

    protected $description = 'Import or update agents and employees from OAM staff monitoring CSV file';

    public function handle()
    {
        $filePath = $this->option('file');
        $isDryRun = $this->option('dry-run');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("Processing OAM staff file: {$filePath}");
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No data will be imported');
        }

        $csvData = $this->readCsv($filePath);
        $agentsProcessed = 0;
        $employeesProcessed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($csvData as $index => $row) {
            try {
                $result = $this->processRow($row, $index + 2, $isDryRun);

                if ($result === 'agent') {
                    $agentsProcessed++;
                } elseif ($result === 'employee') {
                    $employeesProcessed++;
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
        $this->line("✓ Agents processed: {$agentsProcessed}");
        $this->line("✓ Employees processed: {$employeesProcessed}");
        $this->line("- Skipped: {$skipped} staff members");
        $this->line("✗ Errors: {$errors} rows");

        if (!$isDryRun && ($agentsProcessed > 0 || $employeesProcessed > 0)) {
            $this->info("Successfully processed {$agentsProcessed} agents and {$employeesProcessed} employees!");
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
            if (empty($row[1]) || empty(trim($row[1]))) {
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
        $statoOam = trim($row[0] ?? '');
        $nominativo = trim($row[1] ?? '');
        $qualifica = trim($row[2] ?? '');
        $dataInizioOam = trim($row[3] ?? '');
        $dataFineOam = trim($row[4] ?? '');

        // Skip if no name
        if (empty($nominativo)) {
            return 'skipped';
        }

        // Parse OAM start date
        $oamAtDate = $this->parseDate($dataInizioOam);

        // Parse OAM dismissal date (column 5)
        $oamDismissedAtDate = $this->parseDate($dataFineOam);

        // Determine if active based on dismissal date
        $isActive = $oamDismissedAtDate === null && strtoupper($statoOam) === 'ATTIVO';

        // Split name into first and last name
        $nameParts = $this->splitName($nominativo);

        // Process as AGENT if qualification is 'COLLABORATORE'
        if (strtoupper($qualifica) === 'COLLABORATORE') {
            return $this->processAgent($nameParts, $oamAtDate, $oamDismissedAtDate, $isActive, $statoOam, $isDryRun);
        }
        // Process as EMPLOYEE if qualification is NOT 'COLLABORATORE'
        else {
            return $this->processEmployee($nameParts, $oamAtDate, $oamDismissedAtDate, $isActive, $statoOam, $qualifica, $isDryRun);
        }
    }

    private function processAgent(array $nameParts, ?\Carbon\Carbon $oamAtDate, ?\Carbon\Carbon $oamDismissedAtDate, bool $isActive, string $statoOam, bool $isDryRun): string
    {
        // Create full name for Agent model
        $fullName = trim($nameParts['first_name'] . ' ' . $nameParts['last_name']);

        // Try to find existing agent by name
        $existingAgent = $this->findExistingAgent($fullName);

        if ($existingAgent) {
            $needsUpdate = false;
            $updateData = [];

            // Check if OAM date needs updating
            if ($oamAtDate && (!$existingAgent->oam_at || $existingAgent->oam_at->format('Y-m-d') !== $oamAtDate->format('Y-m-d'))) {
                $updateData['oam_at'] = $oamAtDate;
                $needsUpdate = true;
            }

            // Check if OAM dismissal date needs updating
            if ($oamDismissedAtDate !== $existingAgent->oam_dismissed_at) {
                $updateData['oam_dismissed_at'] = $oamDismissedAtDate;
                $needsUpdate = true;
            }

            // Check if active status needs updating
            if ($existingAgent->is_active !== $isActive) {
                $updateData['is_active'] = $isActive;
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                if (!$isDryRun) {
                    $existingAgent->update($updateData);
                    $this->info("✓ Updated agent: {$fullName}");
                    if (isset($updateData['oam_at'])) {
                        $this->line('  OAM: ' . ($updateData['oam_at'] ? $updateData['oam_at']->format('d/m/Y') : 'NULL'));
                    }
                    if (isset($updateData['oam_dismissed_at'])) {
                        $this->line('  Dismissed: ' . ($updateData['oam_dismissed_at'] ? $updateData['oam_dismissed_at']->format('d/m/Y') : 'NULL'));
                    }
                    if (isset($updateData['is_active'])) {
                        $this->line('  Active: ' . ($updateData['is_active'] ? 'Yes' : 'No'));
                    }
                } else {
                    $this->info("Would update agent: {$fullName}");
                    if (isset($updateData['oam_at'])) {
                        $this->line('  OAM: ' . ($updateData['oam_at'] ? $updateData['oam_at']->format('d/m/Y') : 'NULL'));
                    }
                    if (isset($updateData['oam_dismissed_at'])) {
                        $this->line('  Dismissed: ' . ($updateData['oam_dismissed_at'] ? $updateData['oam_dismissed_at']->format('d/m/Y') : 'NULL'));
                    }
                    if (isset($updateData['is_active'])) {
                        $this->line('  Active: ' . ($updateData['is_active'] ? 'Yes' : 'No'));
                    }
                }
                return 'agent';
            } else {
                $this->line("- Agent up to date: {$fullName}");
                return 'skipped';
            }
        }

        // Create new agent if OAM date is available
        if ($oamAtDate) {
            if (!$isDryRun) {
                $agent = Agent::create([
                    'name' => $fullName,
                    'oam_at' => $oamAtDate,
                    'oam_dismissed_at' => $oamDismissedAtDate,
                    'oam' => 'COLLABORATORE',
                    'is_active' => $isActive,
                    'email' => strtolower(str_replace(' ', '.', $fullName)) . '@example.com',
                    'phone' => null,
                    'type' => 'agente',
                    'notes' => "Importato da monitoraggio OAM - Qualifica: COLLABORATORE - Stato: {$statoOam}" . ($oamDismissedAtDate ? " - Revocato: {$oamDismissedAtDate->format('d/m/Y')}" : ''),
                ]);

                $this->info("✓ Created new agent: {$fullName} (OAM: {$oamAtDate->format('d/m/Y')}" . ($oamDismissedAtDate ? ", Dismissed: {$oamDismissedAtDate->format('d/m/Y')}" : '') . ', Active: ' . ($isActive ? 'Yes' : 'No') . ')');
            } else {
                $this->info("Would create new agent: {$fullName} (OAM: {$oamAtDate->format('d/m/Y')}" . ($oamDismissedAtDate ? ", Dismissed: {$oamDismissedAtDate->format('d/m/Y')}" : '') . ', Active: ' . ($isActive ? 'Yes' : 'No') . ')');
            }
            return 'agent';
        }

        $this->line("- Skipped agent (no OAM date): {$fullName}");
        return 'skipped';
    }

    private function processEmployee(array $nameParts, ?\Carbon\Carbon $oamAtDate, ?\Carbon\Carbon $oamDismissedAtDate, bool $isActive, string $statoOam, string $qualifica, bool $isDryRun): string
    {
        // Create full name for Employee model
        $fullName = trim($nameParts['first_name'] . ' ' . $nameParts['last_name']);

        // Try to find existing employee by name
        $existingEmployee = $this->findExistingEmployee($fullName);

        if ($existingEmployee) {
            $needsUpdate = false;
            $updateData = [];

            // Check if OAM date needs updating
            if ($oamAtDate && (!$existingEmployee->oam_at || $existingEmployee->oam_at->format('Y-m-d') !== $oamAtDate->format('Y-m-d'))) {
                $updateData['oam_at'] = $oamAtDate;
                $needsUpdate = true;
            }

            // Check if OAM dismissal date needs updating
            if ($oamDismissedAtDate !== $existingEmployee->oam_dismissed_at) {
                $updateData['oam_dismissed_at'] = $oamDismissedAtDate;
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                if (!$isDryRun) {
                    $existingEmployee->update($updateData);
                    $this->info("✓ Updated employee: {$fullName}");
                    if (isset($updateData['oam_at'])) {
                        $this->line('  OAM: ' . ($updateData['oam_at'] ? $updateData['oam_at']->format('d/m/Y') : 'NULL'));
                    }
                    if (isset($updateData['oam_dismissed_at'])) {
                        $this->line('  Dismissed: ' . ($updateData['oam_dismissed_at'] ? $updateData['oam_dismissed_at']->format('d/m/Y') : 'NULL'));
                    }
                } else {
                    $this->info("Would update employee: {$fullName}");
                    if (isset($updateData['oam_at'])) {
                        $this->line('  OAM: ' . ($updateData['oam_at'] ? $updateData['oam_at']->format('d/m/Y') : 'NULL'));
                    }
                    if (isset($updateData['oam_dismissed_at'])) {
                        $this->line('  Dismissed: ' . ($updateData['oam_dismissed_at'] ? $updateData['oam_dismissed_at']->format('d/m/Y') : 'NULL'));
                    }
                }
                return 'employee';
            } else {
                $this->line("- Employee up to date: {$fullName}");
                return 'skipped';
            }
        }

        // Create new employee if OAM date is available
        if ($oamAtDate) {
            if (!$isDryRun) {
                $employee = Employee::create([
                    'name' => $fullName,
                    'oam_at' => $oamAtDate,
                    'oam_dismissed_at' => $oamDismissedAtDate,
                    'oam' => $qualifica,
                    'email' => strtolower(str_replace(' ', '.', $fullName)) . '@example.com',
                    'phone' => null,
                    'employee_types' => 'dipendente',  // Default to dipendente
                    'notes' => "Importato da monitoraggio OAM - Qualifica: {$qualifica} - Stato: {$statoOam}" . ($oamDismissedAtDate ? " - Revocato: {$oamDismissedAtDate->format('d/m/Y')}" : ''),
                ]);

                $this->info("✓ Created new employee: {$fullName} (OAM: {$oamAtDate->format('d/m/Y')}" . ($oamDismissedAtDate ? ", Dismissed: {$oamDismissedAtDate->format('d/m/Y')}" : '') . ')');
            } else {
                $this->info("Would create new employee: {$fullName} (OAM: {$oamAtDate->format('d/m/Y')}" . ($oamDismissedAtDate ? ", Dismissed: {$oamDismissedAtDate->format('d/m/Y')}" : '') . ')');
            }
            return 'employee';
        }

        $this->line("- Skipped employee (no OAM date): {$fullName}");
        return 'skipped';
    }

    private function splitName(string $fullName): array
    {
        $parts = explode(' ', trim($fullName));

        if (count($parts) === 1) {
            return ['first_name' => $parts[0], 'last_name' => ''];
        }

        // Assume last part is last name, rest is first name
        $lastName = array_pop($parts);
        $firstName = implode(' ', $parts);

        return ['first_name' => $firstName, 'last_name' => $lastName];
    }

    private function findExistingAgent(string $fullName): ?Agent
    {
        // Try exact match
        $agent = Agent::where('name', $fullName)->first();

        if ($agent) {
            return $agent;
        }

        // Try partial match
        $agent = Agent::where('name', 'LIKE', "%{$fullName}%")->first();
        if ($agent) {
            return $agent;
        }

        // Try matching parts of the name
        $nameParts = explode(' ', $fullName);
        foreach ($nameParts as $part) {
            if (strlen($part) > 3) {
                $agent = Agent::where('name', 'LIKE', "%{$part}%")->first();
                if ($agent) {
                    return $agent;
                }
            }
        }

        return null;
    }

    private function findExistingEmployee(string $fullName): ?Employee
    {
        // Try exact match
        $employee = Employee::where('name', $fullName)->first();

        if ($employee) {
            return $employee;
        }

        // Try partial match
        $employee = Employee::where('name', 'LIKE', "%{$fullName}%")->first();
        if ($employee) {
            return $employee;
        }

        // Try matching parts of the name
        $nameParts = explode(' ', $fullName);
        foreach ($nameParts as $part) {
            if (strlen($part) > 3) {
                $employee = Employee::where('name', 'LIKE', "%{$part}%")->first();
                if ($employee) {
                    return $employee;
                }
            }
        }

        return null;
    }

    private function parseDate(string $dateString): ?\Carbon\Carbon
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $dateString);
        } catch (\Exception $e) {
            Log::warning("Could not parse date: {$dateString}");
            return null;
        }
    }
}
