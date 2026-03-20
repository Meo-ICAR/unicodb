<?php

namespace App\Services;

use App\Models\Rui;
use App\Models\RuiAccessoris;
use App\Models\RuiAgentis;
use App\Models\RuiCariche;
use App\Models\RuiCollaboratori;
use App\Models\RuiMandati;
use App\Models\RuiSedi;
use App\Models\RuiSezds;
use App\Models\RuiWebsite;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class RuiCsvImportService
{
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }

    /**
     * Debug: Import only 10 records per file and verify data
     *
     * @return array Import results
     */
    public function debugImportTenRecords(): array
    {
        try {
            $results = [
                'files_processed' => 0,
                'records_imported' => 0,
                'errors' => [],
                'verification' => []
            ];

            $ruiDirectory = public_path('RUI');
            $csvFiles = glob($ruiDirectory . '/*.csv');

            foreach ($csvFiles as $filePath) {
                $fileName = basename($filePath, '.csv');

                Log::info("Debug: Processing {$fileName} - 10 records only");

                $this->processCsvFileWithLimit($filePath, $fileName, $results, 10);
                $results['files_processed']++;

                // Verify the imported data
                $this->verifyImportedData($fileName, $results);
            }

            return $results;
        } catch (\Exception $e) {
            Log::error('Debug import failed: ' . $e->getMessage());
            return [
                'files_processed' => 0,
                'records_imported' => 0,
                'errors' => [$e->getMessage()],
                'verification' => []
            ];
        }
    }

    /**
     * Process CSV file with record limit
     *
     * @param string $filePath
     * @param string $fileName
     * @param array $results
     * @param int $limit
     * @return void
     */
    private function processCsvFileWithLimit(string $filePath, string $fileName, array &$results, int $limit = 10): void
    {
        try {
            $importClass = $this->getImportClass($fileName);
            if (!$importClass) {
                $results['errors'][] = "No import class found for file: {$fileName}";
                return;
            }

            $import = new $importClass($limit, true, $fileName);

            // Create a limited import by reading only first few rows
            $this->importLimitedRows($import, $filePath, $limit);

            $results['records_imported'] += $import->getImportedCount();

            Log::info("Debug: Processed {$fileName} - {$import->getImportedCount()} records");
        } catch (\Exception $e) {
            $results['errors'][] = "Error processing {$fileName}: " . $e->getMessage();
        }
    }

    /**
     * Import limited rows from CSV
     *
     * @param mixed $import
     * @param string $filePath
     * @param int $limit
     * @return void
     */
    private function importLimitedRows($import, string $filePath, int $limit): void
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception("Cannot open file: {$filePath}");
        }

        // Skip header
        fgetcsv($handle, 1000, ';');

        $importedCount = 0;
        while (($row = fgetcsv($handle, 1000, ';')) !== false && $importedCount < $limit) {
            // Convert to associative array with lowercase keys
            $headerRow = $this->getCsvHeaders($filePath);
            $assocRow = [];
            foreach ($headerRow as $index => $header) {
                $assocRow[strtolower($header)] = $row[$index] ?? '';
            }

            // Call the model method and save the result
            $model = $import->model($assocRow);
            if ($model) {
                $model->save();
            }
            $importedCount++;
        }

        fclose($handle);
    }

    /**
     * Get CSV headers
     *
     * @param string $filePath
     * @return array
     */
    private function getCsvHeaders(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        $headers = fgetcsv($handle, 1000, ';');
        fclose($handle);
        return $headers ?: [];
    }

    /**
     * Verify imported data for a table
     *
     * @param string $fileName
     * @param array $results
     * @return void
     */
    private function verifyImportedData(string $fileName, array &$results): void
    {
        $tableMap = [
            'ELENCO_SITO_INTERNET' => 'rui_websites',
            'ELENCO_MANDATI' => 'rui_mandati',
            'ELENCO_COLLABORATORI' => 'rui_collaboratori',
            'ELENCO_COLLABACCESSORI' => 'rui_accessoris',
            'ELENCO_INTERMEDIARI' => 'rui',
            'ELENCO_SEDI' => 'rui_sedi',
            'ELENCO_AG_VEN_PROD_NONST_ISCR_S' => 'rui_agentis',
            'ELENCO_RESP_DISTRIB_SEZ_D' => 'rui_sezds',
            'ELENCO_CARICHE' => 'rui_cariche',
        ];

        $tableName = $tableMap[$fileName] ?? null;
        if (!$tableName) {
            return;
        }

        try {
            $modelClass = $this->getModelClass($tableName);
            if (!$modelClass) {
                return;
            }

            $count = $modelClass::count();
            $firstRecord = $modelClass::first();

            $verification = [
                'table' => $tableName,
                'total_records' => $count,
                'first_record_data' => $firstRecord ? $this->getRecordData($firstRecord) : null,
                'has_data' => $count > 0 && $this->recordHasData($firstRecord)
            ];

            $results['verification'][] = $verification;

            Log::info("Verification for {$tableName}: {$count} records, has_data: " . ($verification['has_data'] ? 'YES' : 'NO'));
        } catch (\Exception $e) {
            $results['errors'][] = "Verification error for {$tableName}: " . $e->getMessage();
        }
    }

    /**
     * Get model class for table name
     *
     * @param string $tableName
     * @return string|null
     */
    private function getModelClass(string $tableName): ?string
    {
        $modelMap = [
            'rui' => 'App\Models\Rui',
            'rui_sedi' => 'App\Models\RuiSedi',
            'rui_mandati' => 'App\Models\RuiMandati',
            'rui_cariche' => 'App\Models\RuiCariche',
            'rui_collaboratori' => 'App\Models\RuiCollaboratori',
            'rui_accessoris' => 'App\Models\RuiAccessoris',
            'rui_agentis' => 'App\Models\RuiAgentis',
            'rui_sezds' => 'App\Models\RuiSezds',
            'rui_websites' => 'App\Models\RuiWebsite',
        ];

        return $modelMap[$tableName] ?? null;
    }

    /**
     * Get record data as array
     *
     * @param mixed $record
     * @return array
     */
    private function getRecordData($record): array
    {
        return $record->toArray();
    }

    /**
     * Check if record has actual data (not just empty strings)
     *
     * @param mixed $record
     * @return bool
     */
    private function recordHasData($record): bool
    {
        if (!$record) {
            return false;
        }

        $data = $record->toArray();
        unset($data['id'], $data['created_at'], $data['updated_at']);

        // Check if any field has non-empty data
        foreach ($data as $value) {
            if ($value !== null && $value !== '' && $value !== '0') {
                return true;
            }
        }

        return false;
    }

    /**
     * Debug: Import only RUI file
     *
     * @return array Import results
     */
    public function debugImportRuiOnly(): array
    {
        try {
            $results = [
                'files_processed' => 0,
                'records_imported' => 0,
                'errors' => []
            ];

            $filePath = public_path('RUI/ELENCO_INTERMEDIARI.csv');
            $fileName = 'ELENCO_INTERMEDIARI';

            Log::info("Debug: Processing RUI file only - {$filePath}");

            $this->processCsvFile($filePath, $fileName, $results);
            $results['files_processed']++;

            Log::info("Debug: RUI import completed - {$results['records_imported']} records");

            return $results;
        } catch (\Exception $e) {
            Log::error('RUI debug import failed: ' . $e->getMessage());
            return [
                'files_processed' => 0,
                'records_imported' => 0,
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Import all RUI CSV files from public/RUI directory (only empty tables)
     *
     * @return array Import results
     */
    public function importAllRuiFiles(): array
    {
        try {
            $results = [
                'files_processed' => 0,
                'records_imported' => 0,
                'errors' => [],
                'skipped_tables' => []
            ];

            $ruiDirectory = public_path('RUI');
            $csvFiles = glob($ruiDirectory . '/*.csv');

            foreach ($csvFiles as $filePath) {
                $fileName = basename($filePath, '.csv');

                // Check if table is empty before importing
                if ($this->isTableEmpty($fileName)) {
                    $this->processCsvFile($filePath, $fileName, $results);
                    $results['files_processed']++;
                } else {
                    $tableName = $this->getTableNameFromFileName($fileName);
                    $results['skipped_tables'][] = [
                        'file' => $fileName,
                        'table' => $tableName,
                        'reason' => 'Table already contains data'
                    ];
                    Log::info("Skipping {$fileName} - table {$tableName} already contains data");
                }
            }

            return $results;
        } catch (\Exception $e) {
            Log::error('RUI import failed: ' . $e->getMessage());
            return [
                'files_processed' => 0,
                'records_imported' => 0,
                'errors' => [$e->getMessage()],
                'skipped_tables' => []
            ];
        }
    }

    /**
     * Import a single specific RUI table (only if table is empty)
     *
     * @param string $tableName The name of the table to import (e.g., 'ELENCO_INTERMEDIARI')
     * @param bool $forceImport Force import even if table contains data
     * @return array Import results
     */
    public function importSingleRuiTable(string $tableName, bool $forceImport = false): array
    {
        try {
            $results = [
                'files_processed' => 0,
                'records_imported' => 0,
                'errors' => [],
                'table_name' => $tableName,
                'skipped' => false
            ];

            // Map table names to file names
            $tableToFileMap = [
                'rui' => 'ELENCO_INTERMEDIARI',
                'rui_sedi' => 'ELENCO_SEDI',
                'rui_mandati' => 'ELENCO_MANDATI',
                'rui_cariche' => 'ELENCO_CARICHE',
                'rui_collaboratori' => 'ELENCO_COLLABORATORI',
                'rui_accessoris' => 'ELENCO_COLLABACCESSORI',
                'rui_agentis' => 'ELENCO_AG_VEN_PROD_NONST_ISCR_S',
                'rui_sezds' => 'ELENCO_RESP_DISTRIB_SEZ_D',
                'rui_websites' => 'ELENCO_SITO_INTERNET',
            ];

            // Allow direct file name as well
            $fileName = $tableToFileMap[$tableName] ?? $tableName;

            // Check if table is empty (unless force import)
            if (!$forceImport && !$this->isTableEmpty($fileName)) {
                $actualTableName = $this->getTableNameFromFileName($fileName);
                $results['skipped'] = true;
                $results['reason'] = "Table {$actualTableName} already contains data";
                Log::info("Skipping {$fileName} - table {$actualTableName} already contains data");
                return $results;
            }

            $filePath = public_path("RUI/{$fileName}.csv");

            if (!file_exists($filePath)) {
                $results['errors'][] = "CSV file not found: {$filePath}";
                return $results;
            }

            Log::info("Importing single RUI table: {$tableName} from file: {$fileName}");

            $this->processCsvFile($filePath, $fileName, $results);
            $results['files_processed']++;

            Log::info("Single RUI table import completed: {$tableName} - {$results['records_imported']} records");

            return $results;
        } catch (\Exception $e) {
            Log::error("Single RUI table import failed for {$tableName}: " . $e->getMessage());
            return [
                'files_processed' => 0,
                'records_imported' => 0,
                'errors' => [$e->getMessage()],
                'table_name' => $tableName,
                'skipped' => false
            ];
        }
    }

    /**
     * Get list of available RUI tables for import
     *
     * @return array Available tables with their descriptions
     */
    public function getAvailableRuiTables(): array
    {
        return [
            'rui' => [
                'file' => 'ELENCO_INTERMEDIARI',
                'description' => 'Intermediari finanziari',
                'model' => 'App\Models\Rui'
            ],
            'rui_sedi' => [
                'file' => 'ELENCO_SEDI',
                'description' => 'Sedi degli intermediari',
                'model' => 'App\Models\RuiSedi'
            ],
            'rui_mandati' => [
                'file' => 'ELENCO_MANDATI',
                'description' => 'Mandati degli intermediari',
                'model' => 'App\Models\RuiMandati'
            ],
            'rui_cariche' => [
                'file' => 'ELENCO_CARICHE',
                'description' => 'Cariche degli intermediari',
                'model' => 'App\Models\RuiCariche'
            ],
            'rui_collaboratori' => [
                'file' => 'ELENCO_COLLABORATORI',
                'description' => 'Collaboratori degli intermediari',
                'model' => 'App\Models\RuiCollaboratori'
            ],
            'rui_accessoris' => [
                'file' => 'ELENCO_COLLABACCESSORI',
                'description' => 'Collaboratori accessori',
                'model' => 'App\Models\RuiAccessoris'
            ],
            'rui_agentis' => [
                'file' => 'ELENCO_AG_VEN_PROD_NONST_ISCR_S',
                'description' => 'Agenti venditori prodotti non strutturati',
                'model' => 'App\Models\RuiAgentis'
            ],
            'rui_sezds' => [
                'file' => 'ELENCO_RESP_DISTRIB_SEZ_D',
                'description' => 'Responsabili distribuzione sezione D',
                'model' => 'App\Models\RuiSezds'
            ],
            'rui_websites' => [
                'file' => 'ELENCO_SITO_INTERNET',
                'description' => 'Siti internet degli intermediari',
                'model' => 'App\Models\RuiWebsite'
            ],
        ];
    }

    /**
     * Clear data for a single specific RUI table
     *
     * @param string $tableName The name of the table to clear
     * @return array Results of the clearing operation
     */
    public function clearSingleRuiTable(string $tableName): array
    {
        try {
            $tableMap = [
                'rui' => 'App\Models\Rui',
                'rui_sedi' => 'App\Models\RuiSedi',
                'rui_mandati' => 'App\Models\RuiMandati',
                'rui_cariche' => 'App\Models\RuiCariche',
                'rui_collaboratori' => 'App\Models\RuiCollaboratori',
                'rui_accessoris' => 'App\Models\RuiAccessoris',
                'rui_agentis' => 'App\Models\RuiAgentis',
                'rui_sezds' => 'App\Models\RuiSezds',
                'rui_websites' => 'App\Models\RuiWebsite',
            ];

            $modelClass = $tableMap[$tableName] ?? null;
            if (!$modelClass) {
                return [
                    'success' => false,
                    'message' => "Invalid table name: {$tableName}"
                ];
            }

            $modelClass::truncate();

            return [
                'success' => true,
                'message' => "Table {$tableName} cleared successfully",
                'table_name' => $tableName
            ];
        } catch (\Exception $e) {
            Log::error("Failed to clear RUI table {$tableName}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'table_name' => $tableName
            ];
        }
    }

    /**
     * Process individual CSV file using Laravel Excel
     *
     * @param string $filePath
     * @param string $fileName
     * @param array $results
     * @return void
     */
    private function processCsvFile(string $filePath, string $fileName, array &$results): void
    {
        try {
            $importClass = $this->getImportClass($fileName);
            if (!$importClass) {
                $results['errors'][] = "No import class found for file: {$fileName}";
                return;
            }

            // Clear memory before processing
            gc_collect_cycles();

            // Enable debug mode to show progress for large imports
            $import = new $importClass(99999999, true, $fileName);

            try {
                Excel::import($import, $filePath);
            } catch (\Illuminate\Database\QueryException $e) {
                // Handle duplicate key errors gracefully
                if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), '1062')) {
                    Log::warning('Duplicate entries found during import, continuing...');
                    // Continue processing - duplicates are already handled in the import class
                } else {
                    // Re-throw other database errors
                    throw $e;
                }
            }

            $results['records_imported'] += $import->getImportedCount();

            // Clear memory after processing
            unset($import);
            gc_collect_cycles();
        } catch (\Exception $e) {
            $results['errors'][] = "Error processing {$fileName}: " . $e->getMessage();
        }
    }

    /**
     * Get the appropriate import class for a CSV file
     *
     * @param string $fileName
     * @return string|null
     */
    private function getImportClass(string $fileName): ?string
    {
        $importClasses = [
            'ELENCO_SITO_INTERNET' => 'App\Imports\RuiWebsitesImport',
            'ELENCO_MANDATI' => 'App\Imports\RuiMandatiImport',
            'ELENCO_COLLABORATORI' => 'App\Imports\RuiCollaboratoriImport',
            'ELENCO_COLLABACCESSORI' => 'App\Imports\RuiAccessorisImport',
            'ELENCO_INTERMEDIARI' => 'App\Imports\RuiIntermediariImport',
            'ELENCO_SEDI' => 'App\Imports\RuiSediImport',
            'ELENCO_AG_VEN_PROD_NONST_ISCR_S' => 'App\Imports\RuiAgentisImport',
            'ELENCO_RESP_DISTRIB_SEZ_D' => 'App\Imports\RuiSezdsImport',
            'ELENCO_CARICHE' => 'App\Imports\RuiCaricheImport',
        ];

        return $importClasses[$fileName] ?? null;
    }

    /**
     * Check if a table is empty based on the file name
     *
     * @param string $fileName The CSV file name
     * @return bool True if table is empty, false otherwise
     */
    private function isTableEmpty(string $fileName): bool
    {
        $tableName = $this->getTableNameFromFileName($fileName);
        if (!$tableName) {
            return true;  // If we can't determine the table, assume it's empty
        }

        $modelClass = $this->getModelClass($tableName);
        if (!$modelClass) {
            return true;  // If we can't find the model, assume it's empty
        }

        try {
            return $modelClass::count() === 0;
        } catch (\Exception $e) {
            Log::warning("Could not check if table {$tableName} is empty: " . $e->getMessage());
            return true;  // Assume empty if we can't check
        }
    }

    /**
     * Get table name from file name
     *
     * @param string $fileName The CSV file name
     * @return string|null The corresponding table name
     */
    private function getTableNameFromFileName(string $fileName): ?string
    {
        $tableMap = [
            'ELENCO_SITO_INTERNET' => 'rui_websites',
            'ELENCO_MANDATI' => 'rui_mandati',
            'ELENCO_COLLABORATORI' => 'rui_collaboratori',
            'ELENCO_COLLABACCESSORI' => 'rui_accessoris',
            'ELENCO_INTERMEDIARI' => 'rui',
            'ELENCO_SEDI' => 'rui_sedi',
            'ELENCO_AG_VEN_PROD_NONST_ISCR_S' => 'rui_agentis',
            'ELENCO_RESP_DISTRIB_SEZ_D' => 'rui_sezds',
            'ELENCO_CARICHE' => 'rui_cariche',
        ];

        return $tableMap[$fileName] ?? null;
    }

    /**
     * Get import statistics for all RUI tables
     *
     * @return array Statistics for each table
     */
    public function getImportStatistics(): array
    {
        return [
            'rui_sedi' => RuiSedi::count(),
            'rui_mandati' => RuiMandati::count(),
            'rui_cariche' => RuiCariche::count(),
            'rui_collaboratori' => RuiCollaboratori::count(),
            'rui_accessoris' => RuiAccessoris::count(),
            'rui_agentis' => RuiAgentis::count(),
            'rui_sezds' => RuiSezds::count(),
            'rui_websites' => RuiWebsite::count(),
        ];
    }

    /**
     * Clear all RUI data (for testing/reset purposes)
     *
     * @return array Results of the clearing operation
     */
    public function clearAllRuiData(): array
    {
        try {
            RuiWebsite::truncate();
            RuiSezds::truncate();
            RuiAgentis::truncate();
            RuiAccessoris::truncate();
            RuiCollaboratori::truncate();
            RuiCariche::truncate();
            RuiMandati::truncate();
            RuiSedi::truncate();
            Rui::truncate();

            return ['success' => true, 'message' => 'All RUI data cleared successfully'];
        } catch (\Exception $e) {
            Log::error('Failed to clear RUI data: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Import first 100 records of RUI with debug and optimization
     *
     * @return array Debug results
     */
    public function debugImportFirst100Rui(): array
    {
        try {
            $startTime = microtime(true);
            $memoryStart = memory_get_usage(true);

            // Clear existing data
            Rui::truncate();

            $filePath = public_path('RUI/ELENCO_INTERMEDIARI.csv');

            if (!file_exists($filePath)) {
                return [
                    'success' => false,
                    'message' => 'RUI CSV file not found',
                    'file_path' => $filePath
                ];
            }

            echo "🔍 Starting debug import of first 100 RUI records...\n";
            echo "📁 File: {$filePath}\n";
            echo '📊 File size: ' . number_format(filesize($filePath) / 1024 / 1024, 2) . " MB\n\n";

            // Optimized Excel import settings
            $config = [
                'memory_limit' => '512M',
                'chunk_size' => 100,  // Process in small chunks for debug
                'batch_size' => 50,  // Smaller batch size for better control
            ];

            // Use Laravel Excel with optimized settings
            $import = new \App\Imports\RuiIntermediariImport(100, true);  // Limit to 100 records, enable debug
            Excel::import($import, $filePath);

            $endTime = microtime(true);
            $memoryEnd = memory_get_usage(true);
            $executionTime = round($endTime - $startTime, 2);
            $memoryUsed = round(($memoryEnd - $memoryStart) / 1024 / 1024, 2);

            $recordCount = Rui::count();

            echo "✅ Import completed!\n";
            echo "📊 Records imported: {$recordCount}\n";
            echo "⏱️  Execution time: {$executionTime} seconds\n";
            echo "💾 Memory used: {$memoryUsed} MB\n";
            echo '🔍 Peak memory: ' . round(memory_get_peak_usage(true) / 1024 / 1024, 2) . " MB\n\n";

            // Show first few records as sample
            $sampleRecords = Rui::take(5)->get(['numero_iscrizione_rui', 'cognome_nome', 'ragione_sociale', 'stato']);
            echo "📋 Sample records:\n";
            foreach ($sampleRecords as $record) {
                echo "   • {$record->numero_iscrizione_rui}: {$record->cognome_nome} / {$record->ragione_sociale} ({$record->stato})\n";
            }

            return [
                'success' => true,
                'records_imported' => $recordCount,
                'execution_time' => $executionTime,
                'memory_used' => $memoryUsed,
                'peak_memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
                'config' => $config
            ];
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            echo "❌ Import failed!\n";
            echo '🔥 Error: ' . $e->getMessage() . "\n";
            echo '📍 Line: ' . $e->getLine() . "\n";
            echo '📁 File: ' . $e->getFile() . "\n";
            echo "⏱️  Time elapsed: {$executionTime} seconds\n";

            Log::error('RUI debug import failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'execution_time' => $executionTime
            ];
        }
    }
}
