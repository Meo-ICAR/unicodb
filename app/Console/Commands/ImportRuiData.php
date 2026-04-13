<?php

namespace App\Console\Commands;

use App\Services\RuiCsvImportService;
use Illuminate\Console\Command;

class ImportRuiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rui:import
                            {--clear : Clear existing data before import}
                            {--stats : Show import statistics after completion}
                            {--force : Force import without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import RUI data from CSV files in public/RUI directory';

    /**
     * The RUI CSV import service instance.
     *
     * @var RuiCsvImportService
     */
    protected $importService;

    /**
     * Import results from the service.
     *
     * @var array
     */
    protected $importResults = [];

    /**
     * Create a new command instance.
     *
     * @param RuiCsvImportService $importService
     */
    public function __construct(RuiCsvImportService $importService)
    {
        parent::__construct();
        $this->importService = $importService;
    }

    /**
     * Execute the console command.
     *
     * Execution flow:
     * 1. If the `--clear` option is set, prompts for confirmation (unless `--force` is also set)
     *    and clears all existing RUI data via the import service.
     * 2. Imports all RUI CSV files found in the `public/RUI/` directory.
     * 3. Displays the import results (files processed, records imported, errors).
     * 4. Optionally shows import statistics before and after the import if `--stats` is set.
     *
     * @return int 0 (Command::SUCCESS) on success, 1 (Command::FAILURE) on error
     */
    public function handle()
    {
        $this->info('🚀 Starting RUI data import...');

        // Clear data if requested
        if ($this->option('clear')) {
            if (!$this->option('force') && !$this->confirm('⚠️  This will delete all existing RUI data. Are you sure?')) {
                $this->info('Import cancelled.');
                return 0;
            }

            $this->info('🗑️  Clearing existing RUI data...');
            $result = $this->importService->clearAllRuiData();

            if (!$result['success']) {
                $this->error('❌ Failed to clear data: ' . $result['message']);
                return 1;
            }

            $this->info('✅ Existing data cleared successfully.');
        }

        // Show current statistics before import
        if ($this->option('stats')) {
            $this->showStatistics('Before Import');
        }

        // Start import
        $this->info('📁 Importing CSV files from public/RUI...');
        $this->withProgressBar(1, function () {
            $results = $this->importService->importAllRuiFiles();
            $this->importResults = $results;
        });

        $this->newLine();
        $this->displayResults();

        // Show statistics after import if requested
        if ($this->option('stats')) {
            $this->showStatistics('After Import');
        }

        $this->info('🎉 RUI data import completed!');
        return 0;
    }

    /**
     * Display import results.
     *
     * @return void
     */
    protected function displayResults()
    {
        $results = $this->importResults ?? [];

        $this->info('📊 Import Results:');
        $filesProcessed = $results['files_processed'] ?? 0;
        $recordsImported = $results['records_imported'] ?? 0;
        $skippedTables = $results['skipped_tables'] ?? [];

        $this->line("   Files processed: {$filesProcessed}");
        $this->line("   Records imported: {$recordsImported}");

        if (!empty($skippedTables)) {
            $this->newLine();
            $this->info('⏭️  Tables skipped (already contain data):');
            foreach ($skippedTables as $skipped) {
                $this->line("   • {$skipped['table']} (file: {$skipped['file']})");
            }
        }

        if (!empty($results['errors'])) {
            $this->newLine();
            $this->error('❌ Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->line("   • {$error}");
            }
        } else {
            $this->info('✅ No errors encountered during import.');
        }
    }

    /**
     * Show import statistics.
     *
     * @param string $title
     * @return void
     */
    protected function showStatistics(string $title)
    {
        $stats = $this->importService->getImportStatistics();

        $this->newLine();
        $this->info("📈 {$title}:");

        foreach ($stats as $table => $count) {
            $formattedTable = ucwords(str_replace('_', ' ', $table));
            $this->line("   {$formattedTable}: " . number_format($count) . ' records');
        }
    }
}
