<?php

namespace App\Services;

use App\Models\Agent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AgentImportService
{
    /**
     * Import Agents from Excel file
     *
     * @param string $filePath Path to the Excel file
     * @param string $companyId Company ID to assign to Agents
     * @return array Import results
     */
    public function importAgents(string $filePath, string $companyId): array
    {
        try {
            $results = [
                'imported' => 0,
                'skipped' => 0,
                'errors' => []
            ];

            // Load Excel file
            $spreadsheet = Excel::toArray([], $filePath);

            // Get 'responsabile esterni' sheet (should be sheet 1 based on debug)
            $sheetData = $this->getSheetData($spreadsheet, 1);  // Use sheet index 1

            if (empty($sheetData)) {
                $results['errors'][] = 'Sheet "responsabile esterni" not found or is empty';
                return $results;
            }

            // Try to find actual Agent data by looking for non-empty, non-formula cells
            $agentRows = [];
            foreach ($sheetData as $rowIndex => $row) {
                // Skip first 2 header rows and look for actual Agent names
                if ($rowIndex < 2) {
                    continue;
                }

                // Skip header rows and look for actual Agent names
                if (isset($row[0]) && !empty($row[0]) && !$this->isFormula($row[0])) {
                    $agentRows[] = $row;
                }
            }

            // Process found Agent rows
            DB::transaction(function () use ($agentRows, $companyId, &$results) {
                foreach ($agentRows as $index => $row) {
                    try {
                        $agentData = $this->mapRowToAgentData($row, $companyId);

                        // Skip rows with empty or formula-based names
                        if (empty($agentData['name']) || $this->isFormula($agentData['name'])) {
                            $results['skipped']++;
                            $this->logDebug('Skipping row ' . ($index + 1) . ': empty name or formula');
                            continue;
                        }

                        // Skip if company doesn't exist
                        if (!DB::table('companies')->where('id', $companyId)->exists()) {
                            $results['errors'][] = "Company ID {$companyId} does not exist";
                            return;
                        }

                        // Check if Agent already exists
                        $existingAgent = Agent::where('company_id', $companyId)
                            ->where('name', $agentData['name'])
                            ->first();

                        if ($existingAgent) {
                            // Skip if agent exists (as requested)
                            $results['skipped']++;
                            continue;
                        } else {
                            // Create new Agent
                            Agent::create($agentData);
                            $results['imported']++;
                        }
                    } catch (\Exception $e) {
                        $results['errors'][] = 'Row ' . ($index + 1) . ': ' . $e->getMessage();
                        $results['skipped']++;
                    }
                }
            });

            return $results;
        } catch (\Exception $e) {
            Log::error('Agent import failed: ' . $e->getMessage());
            return [
                'imported' => 0,
                'skipped' => 0,
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Get specific sheet data from spreadsheet
     *
     * @param array $spreadsheet
     * @param string $sheetName
     * @return array
     */
    private function getSheetData(array $spreadsheet, $sheetIdentifier): array
    {
        foreach ($spreadsheet as $sheetIndex => $sheet) {
            // Try to match sheet by index (0-based) or name
            if ($sheetIndex === $sheetIdentifier || (is_string($sheetIndex) && strtolower($sheetIndex) === strtolower($sheetIdentifier))) {
                return $sheet;
            }
        }

        // If not found by name, try to get the first sheet
        return $spreadsheet[0] ?? [];
    }

    /**
     * Map Excel row to Agent data
     *
     * @param array $row
     * @param string $companyId
     * @return array
     */
    private function mapRowToAgentData(array $row, string $companyId): array
    {
        // Agent structure: ["Nominativo Dipendente","sede","email","Nomina","Data Firma nomina Responsabile al Trattamento ","Data Dimissioni ",...]
        return [
            'company_id' => $companyId,
            'name' => $this->cleanString($row[0] ?? ''),  // Nominativo Dipendente (column A)
            'email' => $this->cleanString($row[1] ?? ''),  // email (column B) - for reference only
            'phone' => $this->truncatePhone($this->cleanString($row[2] ?? '')),  // sede (column C) - for reference only
            'description' => $this->cleanString($row[3] ?? ''),  // Nomina (column D)
            'supervisor_type' => $this->mapSupervisorType($row[3] ?? ''),  // Based on Nomina
            'stipulated_at' => $this->mapDate($row[5] ?? ''),  // Data Firma nomina Responsabile (column F)
            'dismissed_at' => $this->mapDate($row[6] ?? ''),  // Data Dimissioni (column G)
        ];
    }

    /**
     * Clean string value
     *
     * @param mixed $value
     * @return string
     */
    private function cleanString($value): string
    {
        return trim((string) $value);
    }

    /**
     * Check if a string is an Excel formula
     *
     * @param string $value
     * @return bool
     */
    private function isFormula(string $value): bool
    {
        return str_starts_with(trim($value), '=');
    }

    /**
     * Truncate phone number to fit database column
     *
     * @param string $phone
     * @return string
     */
    private function truncatePhone(string $phone): string
    {
        return substr($phone, 0, 16);
    }

    /**
     * Map Agent type to standard value
     *
     * @param string $value
     * @return string
     */
    private function mapAgentType(string $value): string
    {
        $value = strtolower(trim($value));

        return match ($value) {
            'dipendente', 'agent' => 'dipendente',
            'collaboratore', 'collaborator' => 'collaboratore',
            'stagista', 'intern' => 'stagista',
            'consulente', 'consultant' => 'consulente',
            'amministratore', 'administrator' => 'amministratore',
            default => 'dipendente'
        };
    }

    /**
     * Map supervisor type to standard value
     *
     * @param string $value
     * @return string
     */
    private function mapSupervisorType(string $value): string
    {
        $value = strtolower(trim($value));

        return match ($value) {
            'no', 'non', 'non supervisore' => 'no',
            'si', 'sì', 'supervisore' => 'si',
            'filiale', 'supervisore di filiale' => 'filiale',
            default => 'no'
        };
    }

    /**
     * Map string to boolean
     *
     * @param mixed $value
     * @return bool
     */
    private function mapBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim((string) $value));

        return in_array($value, ['sì', 'si', 's', 'yes', 'y', 'true', '1', 'vero']);
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

    /**
     * Import from public/Registro Trattamenti.xlsx
     *
     * @param string $companyId
     * @return array
     */
    public function importFromPublicFile(string $companyId): array
    {
        $filePath = public_path('Registro Trattamenti.xlsx');

        if (!file_exists($filePath)) {
            return [
                'imported' => 0,
                'skipped' => 0,
                'errors' => ["File not found: {$filePath}"]
            ];
        }

        return $this->importAgents($filePath, $companyId);
    }
}
