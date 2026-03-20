<?php

namespace Database\Seeders;

use App\Models\Rui;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = public_path('RUI/ELENCO_INTERMEDIARI.csv');

        if (!file_exists($csvPath)) {
            $this->command->error('CSV file not found: ' . $csvPath);

            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('rui')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $handle = fopen($csvPath, 'r');

        if ($handle === false) {
            $this->command->error('Unable to open CSV file');

            return;
        }

        // Skip BOM and header
        fgets($handle);  // Skip BOM line if present
        $header = fgetcsv($handle, 0, ';');

        $rowCount = 0;
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            try {
                // Convert encoding and clean data
                $row = array_map(function ($field) {
                    $field = iconv('ISO-8859-1', 'UTF-8', $field);
                    $field = trim($field, '" \t\n\r\0\x0B');

                    return $field === '' ? null : $field;
                }, $row);

                Rui::create([
                    'oss' => $row[0] ?? null,
                    'inoperativo' => !empty($row[1]) ? (bool) $row[1] : false,
                    'data_inizio_inoperativita' => $this->parseDate($row[2] ?? null),
                    'numero_iscrizione_rui' => $row[3] ?? null,
                    'data_iscrizione' => $this->parseDate($row[4] ?? null),
                    'cognome_nome' => $row[5] ?? null,
                    'stato' => $row[6] ?? null,
                    'comune_nascita' => $row[7] ?? null,
                    'data_nascita' => $this->parseDate($row[8] ?? null),
                    'ragione_sociale' => $row[9] ?? null,
                    'provincia_nascita' => $row[10] ?? null,
                    'titolo_individuale_sez_a' => $row[11] ?? null,
                    'attivita_esercitata_sez_a' => $row[12] ?? null,
                    'titolo_individuale_sez_b' => $row[13] ?? null,
                    'attivita_esercitata_sez_b' => $row[14] ?? null,
                ]);

                $rowCount++;

                if ($rowCount % 1000 === 0) {
                    $this->command->info("Imported {$rowCount} records...");
                }
            } catch (\Exception $e) {
                $this->command->error("Error importing row {$rowCount}: " . $e->getMessage());

                continue;
            }
        }

        fclose($handle);

        $this->command->info("Successfully imported {$rowCount} RUI records.");
    }

    private function parseDate(?string $date): ?\DateTime
    {
        if (empty($date)) {
            return null;
        }

        // Try different date formats
        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y'];

        foreach ($formats as $format) {
            $dateObj = \DateTime::createFromFormat($format, $date);
            if ($dateObj !== false) {
                return $dateObj;
            }
        }

        return null;
    }
}
