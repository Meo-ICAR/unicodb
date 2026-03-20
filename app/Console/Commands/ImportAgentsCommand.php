<?php

namespace App\Console\Commands;

use App\Services\AgentImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportAgentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agents:import
                            {--company-id : Company ID to assign to agents}
                            {--file= : Path to Excel file (default: public/Registro Trattamenti.xlsx)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import agents from Excel file using sheet responsabile esterni';

    /**
     * Execute the console command.
     *
     * @param AgentImportService $importService
     * @return int
     */
    public function handle(AgentImportService $importService): int
    {
        $companyId = $this->option('company-id');
        if (empty($companyId)) {
            $company = \App\Models\Company::first();
            if (!$company) {
                $this->error('No companies found in database');
                return 1;
            }
            $companyId = $company->id;
        }

        $filePath = $this->option('file');

        $this->info('Starting agent import...');

        if ($filePath) {
            if (!file_exists($filePath)) {
                $this->error("File not found: {$filePath}");
                return 1;
            }

            $results = $importService->importAgents($filePath, $companyId);
        } else {
            $results = $importService->importFromPublicFile($companyId);
        }

        $this->info('Import completed!');
        $this->line("Imported: {$results['imported']} agents");
        $this->line("Skipped: {$results['skipped']} rows");

        if (!empty($results['errors'])) {
            $this->error('Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->line("  - {$error}");
            }
        }

        Log::info('Agent import completed', $results);

        return 0;
    }
}
