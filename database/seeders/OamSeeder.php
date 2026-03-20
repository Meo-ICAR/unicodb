<?php

namespace Database\Seeders;

use App\Models\Oam;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = public_path('EsportazioneElencoMediatoriCreditizi.csv');

        if (!file_exists($csvPath)) {
            $this->command->error('CSV file not found: ' . $csvPath);

            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('oams')->truncate();
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

                Oam::create([
                    'name' => $row[0] ?? null,
                    'autorizzato_ad_operare' => $row[1] ?? null,
                    'persona' => $row[2] ?? null,
                    'codice_fiscale' => $row[3] ?? null,
                    'domicilio_sede_legale' => $row[4] ?? null,
                    'elenco' => $row[5] ?? null,
                    'numero_iscrizione' => $row[6] ?? null,
                    'data_iscrizione' => $this->parseDate($row[7] ?? null),
                    'stato' => $row[8] ?? null,
                    'data_stato' => $this->parseDate($row[9] ?? null),
                    'causale_stato_note' => $row[10] ?? null,
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

        $this->command->info("Successfully imported {$rowCount} OAM records.");
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
