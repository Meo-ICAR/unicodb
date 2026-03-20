<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\TransparencyScanService;
use Illuminate\Console\Command;

class ScanTransparencyLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scan-transparency-links
                            {--limit=10 : Limit number of websites to scan}
                            {--company-id= : Scan only for specific company ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan company websites with transparency dates and extract links from their transparency pages';

    /**
     * The transparency scan service instance.
     *
     * @var TransparencyScanService
     */
    protected $scanService;

    /**
     * Create a new command instance.
     *
     * @param TransparencyScanService $scanService
     */
    public function __construct(TransparencyScanService $scanService)
    {
        parent::__construct();
        $this->scanService = $scanService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Starting transparency links scan...');

        $limit = (int) $this->option('limit');
        $companyId = $this->option('company-id');

        if ($companyId) {
            $this->info("📋 Scanning for company ID: {$companyId}");

            // Perform the scan using the service
            $results = $this->scanService->scanForCompany($companyId, $limit);

            $this->displayResults($results);
        } else {
            $this->info('🔄 Scanning all companies...');

            // Get all companies and scan each
            $companies = Company::all(['id', 'name']);
            $totalProcessed = 0;
            $totalWebsites = 0;
            $totalPages = 0;
            $totalDocuments = 0;
            $allErrors = [];

            foreach ($companies as $company) {
                $this->line("📋 Scanning company: {$company->name} (ID: {$company->id})");

                // Perform the scan using the service
                $results = $this->scanService->scanForCompany($company->id, $limit);

                // Accumulate totals
                $totalProcessed += $results['processed_websites'] ?? 0;
                $totalWebsites += $results['total_websites'] ?? 0;
                $totalPages += $results['found_transparency_pages'] ?? 0;
                $totalDocuments += $results['extracted_documents'] ?? 0;

                // Display company results
                if (!empty($results['details'])) {
                    foreach ($results['details'] as $detail) {
                        $this->line("   • {$detail['domain']}: "
                            . ($detail['found_transparency_page']
                                ? "✓ Found transparency page, {$detail['documents_created']} documents created"
                                : '✗ No transparency page found'));

                        if (!empty($detail['errors'])) {
                            foreach ($detail['errors'] as $error) {
                                $this->line("     ✗ {$error}");
                            }
                        }
                    }
                }

                // Collect errors
                if (!empty($results['errors'])) {
                    $allErrors = array_merge($allErrors, $results['errors']);
                }
            }

            // Display final results
            $this->newLine();
            $this->info('📊 Overall Scan Results:');
            $this->line("   Total companies scanned: {$companies->count()}");
            $this->line("   Total websites processed: {$totalWebsites}");
            $this->line("   Total transparency pages found: {$totalPages}");
            $this->line("   Total documents extracted: {$totalDocuments}");

            if (!empty($allErrors)) {
                $this->newLine();
                $this->error('❌ Errors encountered:');
                foreach ($allErrors as $error) {
                    $this->line("   • {$error}");
                }
            } else {
                $this->newLine();
                $this->info('✅ No errors encountered during scan.');
            }
        }

        $this->newLine();
        $this->info('✅ Transparency links scan completed!');

        return empty($allErrors) ? 0 : 1;
    }

    /**
     * Display scan results
     */
    private function displayResults(array $results): void
    {
        $this->newLine();
        $this->info('📊 Scan Results:');
        $this->line("   Total websites to scan: {$results['total_websites']}");
        $this->line("   Processed websites: {$results['processed_websites']}");
        $this->line("   Found transparency pages: {$results['found_transparency_pages']}");
        $this->line("   Extracted documents: {$results['extracted_documents']}");

        if (!empty($results['details'])) {
            $this->newLine();
            $this->info('📋 Details:');
            foreach ($results['details'] as $detail) {
                $this->line("   • {$detail['domain']}: "
                    . ($detail['found_transparency_page']
                        ? "✓ Found transparency page, {$detail['documents_created']} documents created"
                        : '✗ No transparency page found'));

                if (!empty($detail['errors'])) {
                    foreach ($detail['errors'] as $error) {
                        $this->line("     ✗ {$error}");
                    }
                }
            }
        }

        if (!empty($results['errors'])) {
            $this->newLine();
            $this->error('❌ Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->line("   • {$error}");
            }
        } else {
            $this->newLine();
            $this->info('✅ No errors encountered during scan.');
        }
    }
}
