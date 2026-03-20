<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\EmployeeExcelImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportEmployeesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:import
                            {--company-id= : Company ID to assign to employees}
                            {--file= : Path to Excel file (default: public/Registro Trattamenti.xlsx)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import employees from Excel file using sheet "responsabile interni"';

    /**
     * Execute the console command.
     *
     * @param EmployeeExcelImportService $importService
     * @return int
     */
    public function handle(EmployeeExcelImportService $importService): int
    {
        $companyId = $this->option('company-id');
        if (empty($companyId)) {
            $company = Company::first();
            if (!$company) {
                $this->error('No companies found in database');
                return 1;
            }
            $companyId = $company->id;
        }
        $filePath = $this->option('file');

        $this->info('Starting employee import...');

        if ($filePath) {
            if (!file_exists($filePath)) {
                $this->error("File not found: {$filePath}");
                return 1;
            }

            $results = $importService->importEmployees($filePath, $companyId);
        } else {
            $results = $importService->importFromPublicFile($companyId);
        }

        $this->info('Import completed!');
        $this->line("Imported: {$results['imported']} employees");
        $this->line("Skipped: {$results['skipped']} rows");

        if (!empty($results['errors'])) {
            $this->error('Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->line("  - {$error}");
            }
        }

        Log::info('Employee import completed', $results);

        return 0;
    }
}
