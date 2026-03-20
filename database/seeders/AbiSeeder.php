<?php

namespace Database\Seeders;

use App\Models\Abi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = public_path('abi.csv');

        if (!file_exists($csvPath)) {
            $this->command->error('CSV file not found: ' . $csvPath);

            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Abi::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $handle = fopen($csvPath, 'r');

        if ($handle === false) {
            $this->command->error('Unable to open CSV file');

            return;
        }

        $rowCount = 0;
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            try {
                // Clean and validate data
                $abi = isset($row[0]) ? trim($row[0], '"') : null;
                $name = isset($row[1]) ? trim($row[1], '"') : null;

                // Skip empty rows
                if (empty($abi) && empty($name)) {
                    continue;
                }

                // Determine type based on name or default to BANCA
                $type = 'BANCA';
                if ($name && stripos($name, 'INTERMEDIARIO') !== false) {
                    $type = 'INTERMEDIARIO_106';
                } elseif ($name && (stripos($name, 'IP') !== false || stripos($name, 'IMEL') !== false)) {
                    $type = 'IP_IMEL';
                }

                Abi::create([
                    'abi' => $abi,
                    'name' => $name,
                    'type' => $type,
                    'status' => 'OPERATIVO',
                ]);

                $rowCount++;

                if ($rowCount % 1000 === 0) {
                    $this->command->info("Imported {$rowCount} ABI records...");
                }
            } catch (\Exception $e) {
                $this->command->error("Error importing row {$rowCount}: " . $e->getMessage());

                continue;
            }
        }

        fclose($handle);

        $this->command->info("Successfully imported {$rowCount} ABI records.");
    }
}
