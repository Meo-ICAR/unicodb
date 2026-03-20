<?php

namespace Database\Seeders;

use App\Models\Comune;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Facades\Excel;

class ComuneSeeder extends Seeder implements ToModel, WithChunkReading, WithCustomCsvSettings, WithStartRow
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = public_path('Elenco-comuni-italiani.csv');

        if (!file_exists($csvPath)) {
            $this->command->error('CSV file not found: ' . $csvPath);

            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Comune::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Excel::import($this, $csvPath);
    }

    /**
     * Iniziamo dalla riga 4 per saltare l'intestazione
     */
    public function startRow(): int
    {
        return 4;
    }

    public function model(array $row)
    {
        // Mappatura completa basata sull'ordine delle colonne nel tuo CSV
        return new Comune([
            'codice_regione' => $row[0],
            'codice_unita_territoriale' => $row[1],
            'codice_provincia_storico' => $row[2],
            'progressivo_comune' => $row[3],
            'codice_comune_alfanumerico' => $row[4],
            'denominazione' => $row[5],
            'denominazione_italiano' => $row[6],
            'denominazione_altra_lingua' => $row[7],
            'codice_ripartizione_geografica' => $row[8],
            'ripartizione_geografica' => $row[9],
            'denominazione_regione' => $row[10],
            'denominazione_unita_territoriale' => $row[11],
            'tipologia_unita_territoriale' => $row[12],
            'capoluogo_provincia' => (bool) $row[13],
            'sigla_automobilistica' => $row[14],
            'codice_comune_numerico' => $row[15],
            'codice_comune_110_province' => $row[16],
            'codice_comune_107_province' => $row[17],
            'codice_comune_103_province' => $row[18],
            'codice_catastale' => substr($row[19] ?? '', 0, 4),
            'codice_nuts1_2021' => $row[20],
            'codice_nuts2_2021' => $row[21],
            'codice_nuts3_2021' => $row[22],
            'codice_nuts1_2024' => $row[23],
            'codice_nuts2_2024' => $row[24],
            'codice_nuts3_2024' => $row[25],
        ]);
    }

    /**
     * Configurazione CSV: Punto e virgola e encoding per caratteri italiani
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'input_encoding' => 'Windows-1252',  // Fondamentale per le "u" e "e" accentate dell'ISTAT
        ];
    }

    /**
     * Legge il file a blocchi (ottimo per file grandi)
     */
    public function chunkSize(): int
    {
        return 500;
    }
}
