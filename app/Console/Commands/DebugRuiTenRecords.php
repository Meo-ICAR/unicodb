<?php

namespace App\Console\Commands;

use App\Services\RuiCsvImportService;
use Illuminate\Console\Command;

class DebugRuiTenRecords extends Command
{
    protected $signature = 'rui:debug-ten {--clear : Clear existing data before import}';
    protected $description = 'Debug import only 10 records per RUI file and verify data';

    public function handle()
    {
        $this->info('🔍 Debug RUI Import (10 records per file)...');
        
        $service = new RuiCsvImportService();
        
        if ($this->option('clear')) {
            $this->info('🗑️  Clearing existing RUI data...');
            $clearResult = $service->clearAllRuiData();
            
            if (!$clearResult['success']) {
                $this->error('❌ Failed to clear data: ' . $clearResult['message']);
                return 1;
            }
            
            $this->info('✅ Existing data cleared successfully.');
        }
        
        $this->info('📁 Debug importing 10 records per file...');
        $result = $service->debugImportTenRecords();
        
        $this->info('📊 Debug Import Results:');
        $this->info('   Files processed: ' . $result['files_processed']);
        $this->info('   Records imported: ' . number_format($result['records_imported']));
        
        if (!empty($result['errors'])) {
            $this->error('❌ Errors encountered:');
            foreach ($result['errors'] as $error) {
                $this->error('   • ' . $error);
            }
        } else {
            $this->info('✅ No errors encountered during import.');
        }
        
        $this->newLine();
        $this->info('🔍 Data Verification Results:');
        
        foreach ($result['verification'] as $verification) {
            $status = $verification['has_data'] ? '✅' : '❌';
            $this->line("   {$status} {$verification['table']}: {$verification['total_records']} records");
            
            if (!$verification['has_data'] && $verification['first_record_data']) {
                $this->line('      First record: ' . json_encode($verification['first_record_data'], JSON_UNESCAPED_UNICODE));
            }
        }
        
        $this->info('🎉 Debug RUI import completed!');
        
        return 0;
    }
}
