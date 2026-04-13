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

/**
 * Servizio per l'importazione dei file CSV del RUI (Registro Unico degli Intermediari)
 * dalla directory `public/RUI/` nelle rispettive tabelle del database.
 *
 * I 9 file CSV attesi nella directory `public/RUI/` sono:
 *  - ELENCO_INTERMEDIARI.csv       → tabella `rui`
 *  - ELENCO_SEDI.csv               → tabella `rui_sedi`
 *  - ELENCO_MANDATI.csv            → tabella `rui_mandati`
 *  - ELENCO_CARICHE.csv            → tabella `rui_cariche`
 *  - ELENCO_COLLABORATORI.csv      → tabella `rui_collaboratori`
 *  - ELENCO_COLLABACCESSORI.csv    → tabella `rui_accessoris`
 *  - ELENCO_AG_VEN_PROD_NONST_ISCR_S.csv → tabella `rui_agentis`
 *  - ELENCO_RESP_DISTRIB_SEZ_D.csv → tabella `rui_sezds`
 *  - ELENCO_SITO_INTERNET.csv      → tabella `rui_websites`
 */
class RuiCsvImportService
{
    /**
     * Restituisce le impostazioni di parsing CSV (es. delimitatore).
     *
     * @return array<string, string> Impostazioni CSV
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
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
     * Importa tutti i file CSV RUI dalla directory `public/RUI/`.
     * Salta le tabelle che contengono già dati (a meno che non venga forzato).
     *
     * @return array{files_processed: int, records_imported: int, errors: list<string>, skipped_tables: list<array>} Risultati dell'importazione
     * @throws \Exception In caso di errore critico durante l'importazione
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
     * Importa una singola tabella RUI dal file CSV corrispondente.
     * Salta l'importazione se la tabella contiene già dati, a meno che `$forceImport` sia `true`.
     *
     * @param string $tableName Nome della tabella (es. `rui`, `rui_sedi`) o nome del file CSV (es. `ELENCO_INTERMEDIARI`)
     * @param bool   $forceImport Se `true`, importa anche se la tabella contiene già dati
     * @return array{files_processed: int, records_imported: int, errors: list<string>, table_name: string, skipped: bool} Risultati dell'importazione
     * @throws \Exception In caso di errore critico durante l'importazione
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
     * Restituisce l'elenco delle 9 tabelle RUI disponibili per l'importazione,
     * con il nome del file CSV sorgente, la descrizione e la classe modello associata.
     *
     * @return array<string, array{file: string, description: string, model: string}> Mappa tabella → metadati
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
     * Svuota una singola tabella RUI tramite `TRUNCATE`.
     *
     * @param string $tableName Nome della tabella da svuotare (es. `rui`, `rui_sedi`)
     * @return array{success: bool, message: string, table_name?: string} Risultato dell'operazione
     * @throws \Exception In caso di errore durante il truncate della tabella
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
     * Restituisce il conteggio dei record presenti in ciascuna delle 9 tabelle RUI.
     *
     * @return array<string, int> Mappa tabella → numero di record
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
     * Svuota tutte le 9 tabelle RUI tramite `TRUNCATE` (utile per reset o ambienti di test).
     *
     * @return array{success: bool, message: string} Risultato dell'operazione
     * @throws \Exception In caso di errore durante il truncate di una o più tabelle
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
}
