<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeExcelImportService
{
    /**
     * Import employees from Excel file
     *
     * @param string $filePath Path to the Excel file
     * @param string $companyId Company ID to assign to employees
     * @return array Import results
     */
    public function importEmployees(string $filePath, string $companyId): array
    {
        try {
            $results = [
                'imported' => 0,
                'skipped' => 0,
                'errors' => []
            ];

            // Load Excel file
            $spreadsheet = \Maatwebsite\Excel\Facades\Excel::toArray([], $filePath);

            // Get 'responsabile interni' sheet (should be sheet 2 based on debug)
            $sheetData = $this->getSheetData($spreadsheet, 2);  // Use sheet index 2

            if (empty($sheetData)) {
                $results['errors'][] = 'Sheet "responsabile interni" not found or is empty';
                return $results;
            }

            // Debug: Show specific rows with termination dates
            $terminationDateRows = [];
            foreach ($sheetData as $index => $row) {
                $name = $row[0] ?? '';
                if (strpos(strtoupper($name), 'CACCAVARO EMILIO') !== false) {
                    $terminationDateRows[] = [
                        'row' => $index + 1,
                        'name' => $name,
                        'termination_date_raw' => $row[6] ?? '',
                        'termination_date_parsed' => $this->mapDate($row[6] ?? '')
                    ];
                }
            }

            if (!empty($terminationDateRows)) {
                $this->logDebug('CACCAVARO EMILIO termination date analysis:', $terminationDateRows);
            } else {
                $this->logDebug('CACCAVARO EMILIO not found in data');
            }

            // Also show all rows for CACCAVARO EMILIO to debug
            $caccavaroRows = [];
            foreach ($sheetData as $index => $row) {
                $name = $row[0] ?? '';
                if (strpos(strtoupper($name), 'CACCAVARO EMILIO') !== false) {
                    $caccavaroRows[] = [
                        'row' => $index + 1,
                        'all_columns' => $row,
                        'column_F' => $row[6] ?? 'EMPTY'
                    ];
                }
            }

            $this->logDebug('CACCAVARO EMILIO all rows data:', $caccavaroRows);

            // Try to find actual employee data by looking for non-empty, non-formula cells
            $employeeRows = [];
            foreach ($sheetData as $index => $row) {
                // Skip if row looks like a header (contains empty name or formula-based content)
                if (isset($row[0]) && !empty($row[0]) && !$this->isFormula($row[0])) {
                    $employeeRows[] = $row;
                }
            }

            // Process found employee rows
            DB::transaction(function () use ($employeeRows, $companyId, &$results) {
                foreach ($employeeRows as $index => $row) {
                    try {
                        $employeeData = $this->mapRowToEmployeeData($row, $companyId);

                        // Skip rows with empty or formula-based names
                        if (empty($employeeData['name']) || $this->isFormula($employeeData['name'])) {
                            $results['skipped']++;
                            continue;
                        }

                        // Skip if company doesn't exist
                        if (!DB::table('companies')->where('id', $companyId)->exists()) {
                            $results['errors'][] = "Company ID {$companyId} does not exist";
                            return;
                        }

                        // Check if employee already exists
                        $existingEmployee = Employee::where('company_id', $companyId)
                            ->where('name', $employeeData['name'])
                            ->first();

                        if ($existingEmployee) {
                            // Update existing employee
                            $existingEmployee->update($employeeData);
                            $results['imported']++;
                        } else {
                            // Create new employee
                            Employee::create($employeeData);
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
            Log::error('Employee import failed: ' . $e->getMessage());
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
     * Map Excel row to employee data
     *
     * @param array $row
     * @param string $companyId
     * @return array
     */
    private function mapRowToEmployeeData(array $row, string $companyId): array
    {
        // Sheet 2 structure: ["Nominativo Dipendente","sede","email","Nomina","Data Firma nomina Responsabile al Trattamento ","Data Dimissioni ",...]
        return [
            'company_id' => $companyId,
            'name' => $this->cleanString($row[0] ?? ''),  // Nominativo Dipendente
            'email' => $this->cleanString($row[2] ?? ''),  // email
            'phone' => $this->truncatePhone($this->cleanString($row[1] ?? '')),  // sede (using as phone temporarily)
            'role_title' => $this->cleanString($row[3] ?? ''),  // Nomina
            'department' => $this->cleanString($row[1] ?? ''),  // sede
            'employee_types' => $this->mapEmployeeType($row[3] ?? ''),  // Based on Nomina
            'supervisor_type' => $this->mapSupervisorType($row[3] ?? ''),  // Based on Nomina
            'is_structure' => $this->mapBoolean($row[5] ?? ''),  // Data Firma nomina
            'is_ghost' => $this->mapBoolean($row[6] ?? ''),  // Data Dimissioni
            'hiring_date' => $this->mapDate($row[5] ?? ''),  // Data Firma nomina Responsabile (column F)
            'termination_date' => $this->mapDate($row[6] ?? ''),  // Data Dimissioni (column G)
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
     * Map employee type to standard value
     *
     * @param string $value
     * @return string
     */
    private function mapEmployeeType(string $value): string
    {
        $value = strtolower(trim($value));

        return match ($value) {
            'dipendente', 'employee' => 'dipendente',
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
     * Log debug information
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    private function logDebug(string $message, array $context = []): void
    {
        Log::debug($message, $context);

        // Also output to console for immediate debugging
        echo "[DEBUG] {$message}\n";
        if (!empty($context)) {
            foreach ($context as $key => $value) {
                echo "  {$key}: " . (is_array($value) ? json_encode($value) : $value) . "\n";
            }
            echo "\n";
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

        return $this->importEmployees($filePath, $companyId);
    }
}
