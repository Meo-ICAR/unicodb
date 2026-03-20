<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Models\Agent;
use App\Models\Website;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Reader;

class CheckAgentsFromExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-agents-from-excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check agents from Excel file against database agents';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting agents verification from Excel file...');

        $filePath = public_path('All1_Matrice Segnaletica_MC_Ver_3_0.xlsx');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        try {
            $this->info("Reading file: {$filePath}");

            // Read the Excel file
            $excel = Excel::toArray([], $filePath);

            if (empty($excel)) {
                $this->error('No sheets found in the Excel file');
                return 1;
            }

            $this->info('Found ' . count($excel) . ' sheets in the Excel file');
            $this->newLine();

            // Get Sheet 5 (index 5) - ELENCO SEDI TERRITORIALI
            $sheetIndex = 5;
            $sheet = $excel[$sheetIndex] ?? [];

            if (empty($sheet)) {
                $this->error('Sheet 5 (ELENCO SEDI TERRITORIALI) is empty or not found');
                return 1;
            }

            $this->info('Processing Sheet 5: ELENCO SEDI TERRITORIALI');

            // Get headers from first row
            $headers = $sheet[0] ?? [];
            $this->info('Headers found: ' . implode(', ', array_filter($headers)));

            // Find column H (index 7)
            $columnHIndex = 7;
            $columnHName = $headers[$columnHIndex] ?? 'Column H';

            $this->info("Processing column: {$columnHName} (index {$columnHIndex})");

            // Extract names from column H (skip header row)
            $excelNames = [];
            $totalRows = count($sheet);

            $this->newLine();
            $this->info('=== ALL VALUES IN COLUMN H (Sheet 5: ELENCO SEDI TERRITORIALI) ===');
            $regione = '';

            for ($i = 1; $i < $totalRows; $i++) {
                $row = $sheet[$i];
                $value = trim($row[$columnHIndex] ?? '');

                // Show all non-empty values with row number
                if (!empty($value)) {
                    $this->line("Row {$i}: {$value}");
                    $excelNames[] = $value;
                }
            }

            $this->newLine();
            $this->info('Found ' . count($excelNames) . ' names in column H');

            // Check column I for "NO" values and show corresponding names
            $columnIIndex = 8;  // Column I is index 8
            $columnIName = $headers[$columnIIndex] ?? 'Column I';

            // Get column indexes for address data (B-E)
            $columnBIndex = 1;  // SEDE PRINCIPALE
            $columnNIndex = 2;  // NUMERO
            $columnCIndex = 3;  // CITTA'
            $columnEIndex = 4;  // CAP
            $columnFIndex = 5;  // PROVINCIA

            $this->newLine();
            $this->info("=== CHECKING COLUMN I FOR 'NO' VALUES ===");
            $this->info("Column I: {$columnIName} (index {$columnIIndex})");

            $agentsToAdd = [];

            for ($i = 1; $i < $totalRows; $i++) {
                $row = $sheet[$i];
                $columnHValue = trim($row[$columnHIndex] ?? '');
                $columnIValue = trim($row[$columnIIndex] ?? '');

                if (!empty($columnHValue) && strtoupper($columnIValue) === 'NO') {
                    // Get address data from columns B-E
                    $sedePrincipale = trim($row[$columnBIndex] ?? '');
                    $numeroCivico = trim($row[$columnNIndex] ?? '');
                    $citta = trim($row[$columnCIndex] ?? '');
                    $cap = trim($row[$columnEIndex] ?? '');
                    $provincia = trim($row[$columnFIndex] ?? '');

                    // Parse address components

                    $indirizzo = $sedePrincipale;

                    $this->line("Row {$i}: {$columnHValue} -> Column I: {$columnIValue}");
                    $this->line("  Address: {$indirizzo}, {$numeroCivico}, {$citta}");

                    $agentsToAdd[] = [
                        'name' => $columnHValue,
                        'indirizzo' => $indirizzo,
                        'numero_civico' => $numeroCivico,
                        'citta' => $citta,
                        'zip_code' => $cap,
                        'provincia' => $provincia,
                        'row_index' => $i
                    ];
                }
            }

            $this->newLine();
            $this->info('Found ' . count($agentsToAdd) . " agents to add (Column I = 'NO')");

            // Get all agents from database
            $dbAgents = Agent::pluck('name', 'id')->toArray();
            $this->info('Found ' . count($dbAgents) . ' agents in database');

            // Check each Excel name against database
            $found = [];
            $notFound = [];
            $addedAgents = [];
            $addedAddresses = [];

            foreach ($excelNames as $excelName) {
                // Skip the header
                if ($excelName === 'RESPONSABILE') {
                    continue;
                }

                // Try exact match first
                $foundAgent = array_search($excelName, $dbAgents, true);

                if ($foundAgent !== false) {
                    $found[] = $excelName;

                    // Check if this agent needs an address (column I = 'NO')
                    $agentData = null;
                    foreach ($agentsToAdd as $agentInfo) {
                        if ($agentInfo['name'] === $excelName) {
                            $agentData = $agentInfo;
                            break;
                        }
                    }

                    // Add address for existing agent if needed
                    if ($agentData && (!empty($agentData['indirizzo']) || !empty($agentData['citta']))) {
                        try {
                            // Check if agent already has an address
                            $existingAddress = Address::where('addressable_type', Agent::class)
                                ->where('addressable_id', $foundAgent)
                                ->first();

                            if (!$existingAddress) {
                                Address::create([
                                    'name' => 'Sede Principale',
                                    'street' => $agentData['indirizzo'],
                                    'numero' => $agentData['numero_civico'],
                                    'city' => $agentData['citta'],
                                    'zip_code' => $agentData['zip_code'],
                                    'addressable_type' => Agent::class,
                                    'addressable_id' => $foundAgent,
                                    'address_type_id' => 1,
                                ]);

                                $addedAddresses[] = $excelName;
                                $this->info("  ✓ Added address for existing agent: {$excelName}");
                            } else {
                                $this->info("  - Agent {$excelName} already has address, skipping");
                            }
                        } catch (\Exception $e) {
                            $this->error("  ✗ Failed to add address for existing agent {$excelName}: " . $e->getMessage());
                        }
                    }
                } else {
                    // Try case-insensitive match
                    $foundAgent = array_search(strtolower($excelName), array_map('strtolower', $dbAgents), true);

                    if ($foundAgent !== false) {
                        $found[] = $excelName . ' (case-insensitive match)';

                        // Check if this agent needs an address (column I = 'NO')
                        $agentData = null;
                        foreach ($agentsToAdd as $agentInfo) {
                            if ($agentInfo['name'] === $excelName) {
                                $agentData = $agentInfo;
                                break;
                            }
                        }

                        // Add address for existing agent if needed
                        if ($agentData && (!empty($agentData['indirizzo']) || !empty($agentData['citta']))) {
                            try {
                                $existingAddress = Address::where('addressable_type', Agent::class)
                                    ->where('addressable_id', $foundAgent)
                                    ->first();

                                if (!$existingAddress) {
                                    Address::create([
                                        'name' => 'Sede Principale',
                                        'street' => $agentData['indirizzo'],
                                        'numero' => $agentData['numero_civico'],
                                        'city' => $agentData['citta'],
                                        'zip_code' => $agentData['zip_code'],
                                        'addressable_type' => Agent::class,
                                        'addressable_id' => $foundAgent,
                                        'address_type_id' => 1,
                                    ]);

                                    $addedAddresses[] = $excelName;
                                    $this->info("  ✓ Added address for existing agent: {$excelName}");
                                } else {
                                    $this->info("  - Agent {$excelName} already has address, skipping");
                                }
                            } catch (\Exception $e) {
                                $this->error("  ✗ Failed to add address for existing agent {$excelName}: " . $e->getMessage());
                            }
                        }
                    } else {
                        $notFound[] = $excelName;

                        // Check if this agent should be added (column I = 'NO')
                        $agentData = null;
                        foreach ($agentsToAdd as $agentInfo) {
                            if ($agentInfo['name'] === $excelName) {
                                $agentData = $agentInfo;
                                break;
                            }
                        }

                        if ($agentData) {
                            try {
                                // Get the company ID (assuming first company)
                                $company = \App\Models\Company::first();
                                if (!$company) {
                                    $this->warn('No company found, skipping agent creation');
                                    continue;
                                }

                                // Create the new agent
                                $newAgent = Agent::create([
                                    'company_id' => $company->id,
                                    'name' => $excelName,
                                    'email' => null,
                                    'phone' => null,
                                    'description' => 'Agente aggiunto da Excel - Sheet 5 ELENCO SEDI TERRITORIALI',
                                ]);

                                $addedAgents[] = $excelName;
                                $this->info("✓ Added new agent: {$excelName} (ID: {$newAgent->id})");

                                // Create address record if address data is available
                                if (!empty($agentData['indirizzo']) || !empty($agentData['citta'])) {
                                    try {
                                        Address::create([
                                            'name' => 'Sede Principale',
                                            'street' => $agentData['indirizzo'],
                                            'numero' => $agentData['numero_civico'],
                                            'city' => $agentData['citta'],
                                            'zip_code' => $agentData['zip_code'],
                                            'addressable_type' => Agent::class,
                                            'addressable_id' => $newAgent->id,
                                            'address_type_id' => 1,
                                        ]);

                                        $this->info("  ✓ Added address for: {$excelName}");
                                    } catch (\Exception $e) {
                                        $this->error("  ✗ Failed to add address for {$excelName}: " . $e->getMessage());
                                    }
                                }
                            } catch (\Exception $e) {
                                $this->error("✗ Failed to add agent {$excelName}: " . $e->getMessage());
                            }
                        }
                    }
                }
            }

            // Display results
            $this->newLine();
            $this->info('=== RESULTS ===');
            $this->info('Found in database: ' . count($found));
            $this->info('NOT found in database: ' . count($notFound));
            $this->info('Agents added: ' . count($addedAgents));
            $this->info('Addresses added: ' . count($addedAddresses));

            if (!empty($addedAgents)) {
                $this->newLine();
                $this->info('Agents successfully added:');
                foreach ($addedAgents as $name) {
                    $this->line("  + {$name}");
                }
            }

            if (!empty($addedAddresses)) {
                $this->newLine();
                $this->info('Addresses successfully added to existing agents:');
                foreach ($addedAddresses as $name) {
                    $this->line("  + {$name}");
                }
            }

            if (!empty($notFound)) {
                $this->newLine();
                $this->warn('Names NOT found in agents database:');
                foreach ($notFound as $name) {
                    $shouldAdd = in_array($name, array_column($agentsToAdd, 'name')) ? ' (to add)' : '';
                    $this->line("  - {$name}{$shouldAdd}");
                }
            }

            if (!empty($found)) {
                $this->newLine();
                $this->info('Names found in agents database:');
                foreach ($found as $name) {
                    $this->line("  ✓ {$name}");
                }
            }

            $this->newLine();
            $this->info('Verification completed successfully!');

            // NEW FUNCTION: Read Sheet 4 for names and dates
            $this->newLine();
            $this->newLine();
            $this->info('=== READING SHEET 4: PROFILO INFORMATIVO E DI TRASPARENZA ===');

            // Get Sheet 4 (index 4) - PROFILO INFORMATIVO E DI TRASPARENZA
            $sheet4Index = 4;
            $sheet4 = $excel[$sheet4Index] ?? [];

            if (empty($sheet4)) {
                $this->error('Sheet 4 (PROFILO INFORMATIVO E DI TRASPARENZA) is empty or not found');
                return 1;
            }

            $this->info('Processing Sheet 4: PROFILO INFORMATIVO E DI TRASPARENZA');

            // Read names from cell D4 and continue reading all columns in row 4 until empty values
            $row4Index = 3;  // Row 4 (0-based index 3)
            $row5Index = 4;  // Row 5 (0-based index 4)
            $d4Index = 3;  // Column D (0-based index 3)

            $this->newLine();
            $this->info('=== NAMES FROM ROW 4 (starting from D4) WITH DATES FROM ROW 5 ===');

            $row4Data = $sheet4[$row4Index] ?? [];
            $row5Data = $sheet4[$row5Index] ?? [];
            $foundNames = [];

            // Start from column D and continue until we find empty values
            for ($colIndex = $d4Index; $colIndex < count($row4Data); $colIndex++) {
                $value = trim($row4Data[$colIndex] ?? '');

                if (!empty($value)) {
                    $colLetter = chr(65 + $colIndex);  // Convert index to column letter
                    $this->line("Cell {$colLetter}4: {$value}");

                    // Get corresponding value from row 5 and convert to date
                    $row5Value = trim($row5Data[$colIndex] ?? '');
                    $formattedDate = null;
                    if (!empty($row5Value)) {
                        $formattedDate = $this->mapDate($row5Value);
                        $this->line("Cell {$colLetter}5: {$row5Value}" . ($formattedDate ? " -> {$formattedDate}" : ' (invalid date)'));
                    } else {
                        $this->line("Cell {$colLetter}5: (empty)");
                    }

                    // Add the whole cell value as a single website (no splitting)
                    $foundNames[] = [
                        'name' => $value,
                        'column' => $colLetter,
                        'raw_date' => $row5Value,
                        'formatted_date' => $formattedDate
                    ];

                    // Add record to company_websites table
                    try {
                        // Get the first company ID
                        $company = \App\Models\Company::first();
                        if (!$company) {
                            $this->warn('No company found, skipping website creation');
                            continue;
                        }

                        // Use the full cell value as domain
                        $domain = $value;

                        // Check if website already exists
                        $existingWebsite = \App\Models\Website::where('company_id', $company->id)
                            ->where('domain', $domain)
                            ->first();

                        if (!$existingWebsite) {
                            // Create new company website record
                            $website = \App\Models\Website::create([
                                'company_id' => $company->id,
                                'name' => 'Website from Excel - ' . $colLetter . '4',
                                'domain' => $domain,
                                'type' => 'website',
                                'is_active' => true,
                                'is_typical' => false,
                                'transparency_date' => $formattedDate,
                                'url_privacy' => null,
                                'url_cookies' => null,
                                'is_footercompilant' => false,
                            ]);

                            $this->info("  ✓ Added website: {$domain} (ID: {$website->id})" . ($formattedDate ? " with transparency date: {$formattedDate}" : ''));
                        } else {
                            // Update existing website with transparency date if not set
                            if (!$existingWebsite->transparency_date && $formattedDate) {
                                $existingWebsite->update(['transparency_date' => $formattedDate]);
                                $this->info("  ✓ Updated transparency date for existing website: {$domain} -> {$formattedDate}");
                            } else {
                                $this->info("  - Website {$domain} already exists, skipping");
                            }
                        }
                    } catch (\Exception $e) {
                        $this->error("  ✗ Failed to create/update website for {$value}: " . $e->getMessage());
                    }

                    $this->line('');  // Add empty line for readability
                } else {
                    // Stop when we find an empty cell
                    $this->info('Stopped at column ' . chr(65 + $colIndex) . '4 (empty value)');
                    break;
                }
            }

            if (!empty($foundNames)) {
                $this->info('Total entries found: ' . count($foundNames));
                foreach ($foundNames as $index => $entry) {
                    $dateInfo = $entry['formatted_date'] ? " (Date: {$entry['formatted_date']})" : ' (No valid date)';
                    $this->line('  ' . ($index + 1) . ". {$entry['name']} [{$entry['column']}4]{$dateInfo}");
                }
            } else {
                $this->line('No names found in row 4 starting from D4');
            }

            // Read dates from row 5 (row 4 - 0-based)
            $this->newLine();
            $this->info('=== DATES FROM ROW 5 ===');

            $row5Data = $sheet4[$row5Index] ?? [];
            $foundDates = [];

            foreach ($row5Data as $colIndex => $cellValue) {
                $value = trim($cellValue);
                if (!empty($value)) {
                    // Try to detect if it's a date
                    if (preg_match('/\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{2,4}|\d{4}[\/\-\.]\d{1,2}[\/\-\.]\d{1,2}/', $value)) {
                        $colLetter = chr(65 + $colIndex);  // Convert index to column letter
                        $foundDates[] = "Cell {$colLetter}5: {$value}";
                    }
                }
            }

            if (!empty($foundDates)) {
                foreach ($foundDates as $dateInfo) {
                    $this->line($dateInfo);
                }
                $this->info('Found ' . count($foundDates) . ' dates in row 5');
            } else {
                $this->line('No dates found in row 5');
            }

            // Also show all non-empty values in row 5 for context
            $this->newLine();
            $this->info('=== ALL VALUES IN ROW 5 ===');
            foreach ($row5Data as $colIndex => $cellValue) {
                $value = trim($cellValue);
                if (!empty($value)) {
                    $colLetter = chr(65 + $colIndex);
                    $this->line("Cell {$colLetter}5: {$value}");
                }
            }
        } catch (\Exception $e) {
            $this->error('Error processing Excel file: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Map date string to date format
     *
     * @param mixed $value
     * @return string|null
     */
    private function mapDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Handle Excel serial numbers (Excel epoch where 1 = 1900-01-01)
        if (is_numeric($value) && $value > 10000) {
            try {
                // Convert Excel serial number to date
                $date = \Carbon\Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($value - 1);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        try {
            $date = \Carbon\Carbon::parse($value);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
