<?php

namespace App\Console\Commands;

use App\Services\PracticeExcelImportService;
use Illuminate\Console\Command;

class ImportPracticeDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'practice:import-excel {file? : Path to Excel file (optional, defaults to public/Estrazioneprimosemestre25.xlsx)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import practice data from Excel file and update practices based on CRM_code';

    /**
     * Execute the console command.
     */
    public function handle(PracticeExcelImportService $importService)
    {
        $this->info('Starting practice data import...');

        $filePath = $this->argument('file');

        if (empty($filePath)) {
            $this->info('Using default file: public/Estrazioneprimosemestre25.xlsx');
            $results = $importService->importFromPublicFile();
        } else {
            $this->info("Using file: {$filePath}");

            if (!file_exists($filePath)) {
                $this->error("File not found: {$filePath}");
                return 1;
            }

            $results = $importService->importPracticeUpdates($filePath);
        }

        // Display results
        $this->newLine();
        $this->info('Import Results:');
        $this->line("✅ Updated: {$results['updated']} practices");
        $this->line("❌ Not found: {$results['not_found']} CRM codes");

        if (!empty($results['not_found_codes'])) {
            $this->newLine();
            $this->error('CRM codes not found:');
            foreach ($results['not_found_codes'] as $code) {
                $this->line("  - {$code}");
            }
        }

        if (!empty($results['errors'])) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->line("  - {$error}");
            }
        }

        $this->newLine();
        $this->info('Import completed successfully!');

        return 0;
    }
}
