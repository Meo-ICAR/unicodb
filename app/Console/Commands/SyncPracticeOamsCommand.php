<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\PracticeOamService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncPracticeOamsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oam:import {--company-id= : Company ID (optional, will use first company if not provided)} {--start-date= : Start date (optional, will calculate based on current date)} {--end-date= : End date (optional, will calculate as startDate + 6 months)} {--stats : Show statistics only without syncing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync practice_oams data for a company with date filtering';

    /**
     * Execute the console command.
     */
    public function handle(PracticeOamService $service)
    {
        $companyId = $this->option('company-id');
        $startDate = $this->option('start-date');
        $endDate = $this->option('end-date');
        $statsOnly = $this->option('stats');

        if (empty($companyId)) {
            $companyId = Company::first()->id;
        }

        if (empty($startDate)) {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
            if (now()->month < 5) {
                $startDate = Carbon::parse($startDate)->subMonths(6)->format('Y-m-d');
            }
        }

        $this->info('Practice OAM Sync Command');
        $this->info('========================');
        $this->info('Company ID: ' . ($companyId ?: 'First available company'));
        $this->info("Start Date: {$startDate}");
        $this->info("End Date: {$endDate}");
        $this->info('Stats Only: ' . ($statsOnly ? 'Yes' : 'No'));
        $this->newLine();

        // If endDate is null, calculate and set it
        if (empty($endDate)) {
            $endDate = Carbon::parse($startDate)->addMonths(6)->format('Y-m-d');
            $this->info("Note: End Date will be calculated as: {$endDate}");
            $this->newLine();
        }

        try {
            if ($statsOnly) {
                $this->showStats($service, $companyId, $startDate, $endDate);
            } else {
                $this->performSync($service, $companyId, $startDate, $endDate);
            }

            $this->info('Operation completed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }

    private function showStats(PracticeOamService $service, ?string $companyId, ?string $startDate, ?string $endDate)
    {
        $this->info('Showing statistics...');

        try {
            $stats = $service->getSyncStats($companyId, $startDate, $endDate);

            $this->info('Stats retrieved successfully');
            $this->info('Total Practices: ' . $stats['total_practices']);
            $this->info('Eligible Practices: ' . $stats['eligible_practices']);
            $this->info('Current Practice OAMs: ' . $stats['current_practice_oams']);
            $this->info('Needs Sync: ' . ($stats['needs_sync'] ? 'Yes' : 'No'));

            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total Practices', $stats['total_practices']],
                    ['Eligible Practices', $stats['eligible_practices']],
                    ['Current Practice OAMs', $stats['current_practice_oams']],
                    ['Needs Sync', $stats['needs_sync'] ? 'Yes' : 'No'],
                ]
            );

            if ($stats['needs_sync']) {
                $this->newLine();
                $this->warn('Sync is needed. Run without --stats to perform the sync.');
            } else {
                $this->newLine();
                $this->info('Data is already in sync.');
            }
        } catch (\Exception $e) {
            $this->error('Error getting stats: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            throw $e;
        }
    }

    private function performSync(PracticeOamService $service, ?string $companyId, ?string $startDate, ?string $endDate)
    {
        $this->info('Starting sync process...');

        // Show before stats
        $beforeStats = $service->getSyncStats($companyId, $startDate, $endDate);
        $this->info("Before sync: {$beforeStats['current_practice_oams']} practice_oam records");

        // Perform sync
        $this->withProgressBar(1, function () use ($service, $companyId, $startDate, $endDate) {
            $service->syncPracticeOamsForCompany($companyId, $startDate, $endDate);
        });

        $this->newLine();

        // Show after stats
        $afterStats = $service->getSyncStats($companyId, $startDate, $endDate);
        $this->info("After sync: {$afterStats['current_practice_oams']} practice_oam records");
        $this->info("Eligible practices processed: {$afterStats['eligible_practices']}");

        if ($afterStats['needs_sync']) {
            $this->warn('Warning: Sync completed but data may still be out of sync.');
        } else {
            $this->info('✓ Data is now in sync.');
        }
    }
}
