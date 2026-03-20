<?php

namespace App\Console\Commands;

use App\Services\RuiCsvImportService;
use Illuminate\Console\Command;

class ImportSingleRuiTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rui:import-single
                            {table : The name of the RUI table to import}
                            {--clear : Clear existing data before import}
                            {--list : List all available RUI tables}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a single RUI table from CSV file';

    /**
     * The RUI CSV import service instance.
     *
     * @var RuiCsvImportService
     */
    protected $importService;

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
     * @return int
     */
    public function handle()
    {
        if ($this->option('list')) {
            $this->listAvailableTables();
            return 0;
        }

        $tableName = $this->argument('table');

        if (empty($tableName)) {
            $this->error('❌ Table name is required. Use --list to see available tables.');
            return 1;
        }

        $this->info("🚀 Starting RUI import for table: {$tableName}");

        // Clear data if requested
        if ($this->option('clear')) {
            if (!$this->confirm('⚠️  This will delete all existing data for table ' . $tableName . '. Are you sure?')) {
                $this->info('Import cancelled.');
                return 0;
            }

            $this->info('🗑️  Clearing existing data...');
            $result = $this->importService->clearSingleRuiTable($tableName);

            if (!$result['success']) {
                $this->error('❌ Failed to clear data: ' . $result['message']);
                return 1;
            }

            $this->info('✅ Existing data cleared successfully.');
        }

        // Start import
        $this->info('📁 Importing CSV file...');
        $results = $this->importService->importSingleRuiTable($tableName);

        $this->displayResults($results);

        if (empty($results['errors'])) {
            $this->info('🎉 RUI import completed successfully!');
            return 0;
        } else {
            $this->error('❌ RUI import completed with errors.');
            return 1;
        }
    }

    /**
     * Display import results.
     *
     * @param array $results
     * @return void
     */
    protected function displayResults(array $results): void
    {
        $this->newLine();
        $this->info('📊 Import Results:');
        $this->line('   Table: ' . ($results['table_name'] ?? 'Unknown'));
        $this->line("   Files processed: {$results['files_processed']}");
        $this->line("   Records imported: {$results['records_imported']}");

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
     * List all available RUI tables.
     *
     * @return void
     */
    protected function listAvailableTables(): void
    {
        $tables = $this->importService->getAvailableRuiTables();

        $this->info('📋 Available RUI Tables:');
        $this->newLine();

        foreach ($tables as $key => $table) {
            $this->line("   <info>{$key}</info>");
            $this->line("   File: {$table['file']}.csv");
            $this->line("   Description: {$table['description']}");
            $this->line("   Model: {$table['model']}");
            $this->newLine();
        }

        $this->info('Usage examples:');
        $this->line('   php artisan rui:import-single rui');
        $this->line('   php artisan rui:import-single rui --clear');
        $this->line('   php artisan rui:import-single ELENCO_INTERMEDIARI --clear');
    }
}
