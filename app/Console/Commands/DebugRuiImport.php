<?php

namespace App\Console\Commands;

use App\Services\RuiCsvImportService;
use Illuminate\Console\Command;

class DebugRuiImport extends Command
{
    protected $signature = 'rui:debug-import {--clear : Clear existing data before import}';
    protected $description = 'Debug import only RUI data from CSV';

    public function handle()
    {
        $this->info('🔍 Debug RUI Import Starting...');
        
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
        
        $this->info('📈 Before Import:');
        $this->info('   Rui: ' . number_format(\App\Models\Rui::count()) . ' records');
        
        $this->info('📁 Debug importing RUI file only...');
        $result = $service->debugImportRuiOnly();
        
        $this->info('📊 Debug Import Results:');
        $this->info('   Files processed: ' . $result['files_processed']);
        $this->info('   Records imported: ' . number_format($result['records_imported']));
        
        if (!empty($result['errors'])) {
            $this->error('❌ Errors encountered:');
            foreach ($result['errors'] as $error) {
                $this->error('   - ' . $error);
            }
        } else {
            $this->info('✅ No errors encountered during import.');
        }
        
        $this->info('📈 After Import:');
        $this->info('   Rui: ' . number_format(\App\Models\Rui::count()) . ' records');
        
        $this->info('🎉 Debug RUI import completed!');
        
        return 0;
    }
}
