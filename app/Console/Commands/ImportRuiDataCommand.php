<?php

namespace App\Console\Commands;

use App\Services\RuiCsvImportService;
use Illuminate\Console\Command;

class ImportRuiDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rui:import-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all RUI CSV files from public/RUI directory';

    /**
     * Execute the console command.
     */
    public function handle(RuiCsvImportService $importService)
    {
        $this->info('Starting RUI data import...');
        $this->info('Processing all CSV files in public/RUI directory...');

        $results = $importService->importAllRuiFiles();

        // Display results
        $this->newLine();
        $this->info('Import Results:');
        $this->line("✅ Files processed: {$results['files_processed']}");
        $this->line("✅ Records imported: {$results['records_imported']}");

        if (!empty($results['errors'])) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->line("  - {$error}");
            }
        }

        $this->newLine();
        $this->info('RUI import completed successfully!');

        return 0;
    }
}
