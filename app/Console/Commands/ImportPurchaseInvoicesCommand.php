<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Client;
use App\Models\Company;
use App\Models\PurchaseInvoice;
use App\Services\PurchaseInvoiceImportService;
use Illuminate\Console\Command;

class ImportPurchaseInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purchase-invoices:import {--company= : Company ID} {--file= : CSV file path} {--force : Force reimport}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import purchase invoices from CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting purchase invoices import...');

        // Get company
        $companyId = $this->option('company') ?: Company::first()->id;

        if ($companyId) {
            $company = Company::findOrFail($companyId);
        } else {
            $companies = Company::all();
            if ($companies->isEmpty()) {
                $this->error('No companies found');
                return 1;
            }

            $companyId = $this->choice('Select company', $companies->pluck('name', 'id')->toArray());
            $company = Company::findOrFail($companyId);
        }

        $this->info("Using company: {$company->name} (ID: {$company->id})");

        // Get file path
        $filePath = $this->option('file') ?? public_path('Fatture acquisto reg.csv');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Importing from: {$filePath}");

        // Check existing records
        $existingCount = PurchaseInvoice::where('company_id', $company->id)->count();
        if ($existingCount > 0 && !$this->option('force')) {
            if (!$this->confirm("Found {$existingCount} existing invoices for this company. Continue import?")) {
                $this->info('Import cancelled.');
                return 0;
            }
        }

        // Perform import
        try {
            $filename = basename($filePath);
            $importService = new PurchaseInvoiceImportService($company->id, $filename);
            $results = $importService->import($filePath, $company->id);

            // Display results
            $this->newLine();
            $this->info('Import Results:');
            $this->info('===============');
            $this->line("Imported: {$results['imported']}");
            $this->line("Updated: {$results['updated']}");
            $this->line("Skipped: {$results['skipped']}");
            $this->line("Errors: {$results['errors']}");

            if (!empty($results['details'])) {
                $this->newLine();
                $this->info('Details (first 10):');
                foreach (array_slice($results['details'], 0, 10) as $detail) {
                    $this->line("- {$detail}");
                }

                if (count($results['details']) > 10) {
                    $this->line('... and ' . (count($results['details']) - 10) . ' more details');
                }
            }

            // Verify import
            $newCount = PurchaseInvoice::where('company_id', $company->id)->count();
            $this->newLine();
            $this->info('Verification:');
            $this->line("Total invoices in database: {$newCount}");

            if ($newCount > $existingCount) {
                $this->info('✓ Import successful! ' . ($newCount - $existingCount) . ' new invoices added.');
            } else {
                $this->warn('No new invoices were added to the database.');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile());
            $this->error('Line: ' . $e->getLine());
            return 1;
        }
    }
}
