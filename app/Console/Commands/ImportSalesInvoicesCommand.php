<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Company;
use App\Models\Principal;
use App\Models\SalesInvoice;
use App\Services\SalesInvoiceImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportSalesInvoicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales-invoices:import {--company= : Company ID} {--file= : Custom file path} {--force : Force reimport}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import sales invoices from CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companyId = $this->option('company');
        $filePath = $this->option('file');
        $force = $this->option('force');

        $this->info('Starting sales invoices import...');
        // Get company
        $companyId = $this->option('company') ?: Company::first()->id;

        try {
            // Get company
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

            // Determine file path
            if ($filePath) {
                if (!file_exists($filePath)) {
                    $this->error("File not found: {$filePath}");
                    return 1;
                }
            } else {
                $filePath = base_path('public/Fatture vendita registrate 2025.csv');
                if (!file_exists($filePath)) {
                    $this->error("Default file not found: {$filePath}");
                    return 1;
                }
            }

            $this->info("Importing from: {$filePath}");

            // Check for existing invoices
            $existingCount = SalesInvoice::where('company_id', $company->id)->count();
            if ($existingCount > 0 && !$force) {
                $this->warn("Found {$existingCount} existing invoices for this company.");
                if (!$this->confirm('Continue import? (Duplicates will be updated)')) {
                    $this->info('Import cancelled');
                    return 0;
                }
            }

            // Perform import
            $filename = basename($filePath);
            $importService = new SalesInvoiceImportService($filename);
            $results = $importService->import($filePath, $company->id);

            // Display results
            $this->displayResults($results, $company->id);

            return 0;
        } catch (\Exception $e) {
            $this->error('Import failed: ' . $e->getMessage());
            Log::error('Sales invoice import command failed', [
                'company_id' => $companyId,
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return 1;
        }
    }

    protected function displayResults($results, $companyId)
    {
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

        // Verification
        $totalInvoices = SalesInvoice::where('company_id', $companyId)->count();
        $this->newLine();
        $this->info('Verification:');
        $this->line("Total invoices in database: {$totalInvoices}");

        if ($results['imported'] > 0) {
            $this->info("✓ Import successful! {$results['imported']} new invoices added.");
        } elseif ($results['updated'] > 0) {
            $this->info("✓ Import successful! {$results['updated']} invoices updated.");
        } else {
            $this->warn('No new invoices were added to the database.');
        }
    }
}
