<?php

namespace App\Services;

use App\Models\Practice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PracticeExcelImportService
{
    /**
     * Import practice updates from Excel file
     *
     * @param string $filePath Path to Excel file
     * @return array Import results
     */
    public function importPracticeUpdates(string $filePath): array
    {
        try {
            $results = [
                'updated' => 0,
                'not_found' => 0,
                'not_found_codes' => [],
                'errors' => []
            ];

            // Load Excel file
            $spreadsheet = \Maatwebsite\Excel\Facades\Excel::toArray([], $filePath);

            // Get first sheet
            $sheetData = $this->getSheetData($spreadsheet, 0);

            if (empty($sheetData)) {
                $results['errors'][] = 'Excel file is empty or could not be read';
                return $results;
            }

            // Process each row (skip first row as it's a header)
            DB::transaction(function () use ($sheetData, &$results) {
                foreach ($sheetData as $index => $row) {
                    // Skip header row (index 0)
                    if ($index === 0) {
                        continue;
                    }
                    try {
                        $practiceData = $this->mapRowToPracticeData($row);

                        // Skip rows without CRM_code
                        if (empty($practiceData['CRM_code'])) {
                            continue;
                        }

                        // Find practice by CRM_code
                        $practice = Practice::where('CRM_code', $practiceData['CRM_code'])->first();

                        if (!$practice) {
                            $results['not_found']++;
                            $results['not_found_codes'][] = $practiceData['CRM_code'];
                            continue;
                        }

                        // Update practice with new values
                        $updateData = array_filter([
                            'net' => $practiceData['net'],
                            'amount' => $practiceData['amount'],
                            'principal_fee' => $practiceData['principal_fee'],
                            'brokerage_fee' => $practiceData['brokerage_fee'],
                            //   'client_fee' => $practiceData['client_fee'],
                            'sended_at' => $practiceData['sended_at'],
                            'approved_at' => $practiceData['approved_at'],
                            'erogated_at' => $practiceData['erogated_at'],
                        ], function ($value) {
                            return $value !== null;
                        });

                        if (!empty($updateData)) {
                            $practice->update($updateData);
                            $results['updated']++;
                        }
                    } catch (\Exception $e) {
                        $results['errors'][] = 'Row ' . ($index + 1) . ': ' . $e->getMessage();
                        $this->logDebug('Error processing row ' . ($index + 1), ['error' => $e->getMessage()]);
                    }
                }
            });

            return $results;
        } catch (\Exception $e) {
            Log::error('Practice import failed: ' . $e->getMessage());
            return [
                'updated' => 0,
                'not_found' => 0,
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Get specific sheet data from spreadsheet
     *
     * @param array $spreadsheet
     * @param int $sheetIndex
     * @return array
     */
    private function getSheetData(array $spreadsheet, int $sheetIndex): array
    {
        return $spreadsheet[$sheetIndex] ?? [];
    }

    /**
     * Map Excel row to practice data
     *
     * @param array $row
     * @return array
     */
    private function mapRowToPracticeData(array $row): array
    {
        return [
            'CRM_code' => $this->cleanString($row[1] ?? ''),  // Column B (index 1)
            'net' => $this->cleanDecimal($row[11] ?? ''),  // Column L (index 11)
            'amount' => $this->cleanDecimal($row[12] ?? ''),  // Column M (index 12)
            'principal_fee' => $this->cleanDecimal($row[13] ?? ''),  // Column N (index 13)
            'brokerage_fee' => $this->cleanDecimal($row[14] ?? ''),  // Column O (index 14)
            'sended_at' => $this->mapDate($row[29] ?? ''),  // Column AD (index 29)
            'approved_at' => $this->mapDate($row[30] ?? ''),  // Column AE (index 30)
            'erogated_at' => $this->mapDate($row[31] ?? ''),  // Column AF (index 31)
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
     * Clean decimal value
     *
     * @param mixed $value
     * @return float|null
     */
    private function cleanDecimal($value): ?float
    {
        if (empty($value)) {
            return null;
        }

        // Remove common formatting characters
        $cleaned = str_replace(['€', ',', ' '], ['', '.', ''], (string) $value);

        if (!is_numeric($cleaned)) {
            return null;
        }

        return (float) $cleaned;
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
     * Import from public/Estrazioneprimosemestre25.xlsx
     *
     * @return array
     */
    public function importFromPublicFile(): array
    {
        $filePath = public_path('Estrazioneprimosemestre25.xlsx');

        if (!file_exists($filePath)) {
            return [
                'updated' => 0,
                'not_found' => 0,
                'errors' => ["File not found: {$filePath}"]
            ];
        }

        return $this->importPracticeUpdates($filePath);
    }
}
