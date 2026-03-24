<?php

namespace Database\Seeders;

use App\Models\OAMSoggetti;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class OAMSoggettiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = public_path('exportSoggetti.csv');

        if (!file_exists($csvPath)) {
            $this->command->error('CSV file not found: ' . $csvPath);
            return;
        }

        $this->command->info('🔧 Importing OAMSoggetti from CSV...');

        // Disable foreign key checks and truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        OAMSoggetti::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $startTime = microtime(true);

        try {
            // Import all records without limit
            $this->command->info('📊 Importing all OAMSoggetti records...');
            $import = new \App\Imports\OAMSoggettiImport(99999999, true, 'exportSoggetti');  // Enable debug mode
            Excel::import($import, $csvPath);

            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            $importedCount = $import->getImportedCount();
            $this->command->info("✅ Processed {$importedCount} OAMSoggetti records in {$executionTime} seconds");

            // Force commit any pending transactions
            DB::commit();

            // Verify import - check immediately after import
            $totalRecords = OAMSoggetti::count();
            $this->command->info("📊 Total OAMSoggetti records in database: {$totalRecords}");

            if ($totalRecords === 0) {
                $this->command->error('❌ WARNING: No records were actually imported! Check for validation errors.');
            }
        } catch (\Exception $e) {
            $this->command->error('❌ Failed to import OAMSoggetti: ' . $e->getMessage());
            $this->command->error('Line: ' . $e->getLine());
            $this->command->error('File: ' . $e->getFile());

            // Check if it's a validation error
            if (str_contains($e->getMessage(), 'validation')) {
                $this->command->error('💡 This appears to be a validation error. Check field requirements.');
            }
        }

        $this->command->info('✅ OAMSoggetti import completed!');
    }
}
