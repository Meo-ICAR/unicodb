<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinancialSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = storage_path('app/private/COD_ABI.TXT');

        if (! file_exists($filePath)) {
            Log::error("File COD_ABI.TXT not found at: {$filePath}");

            return;
        }

        $handle = fopen($filePath, 'r');
        if (! $handle) {
            Log::error("Unable to open file: {$filePath}");

            return;
        }

        $financials = [];
        $batchSize = 1000;
        $processed = 0;

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Parse CSV format: "ABI_CODE","NAME"
            if (preg_match('/^"(\d{5})","([^"]+)"$/', $line, $matches)) {
                $financials[] = [
                    'abi_code' => $matches[1],
                    'name' => $matches[2],
                    'type' => 'BANCA',  // Default type, can be updated later
                    'status' => 'OPERATIVO',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Insert in batches to avoid memory issues
                if (count($financials) >= $batchSize) {
                    DB::table('financials')->insert($financials);
                    $processed += count($financials);
                    $financials = [];
                    echo "Processed {$processed} financial institutions...\n";
                }
            }
        }

        // Insert remaining records
        if (! empty($financials)) {
            DB::table('financials')->insert($financials);
            $processed += count($financials);
        }

        fclose($handle);

        echo "Total financial institutions imported: {$processed}\n";
        Log::info("FinancialSeeder completed: {$processed} institutions imported");
    }
}
