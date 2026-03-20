<?php

namespace App\Imports;

use App\Models\RuiAccessoris;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RuiAccessorisImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithCustomCsvSettings
{
    protected $importedCount = 0;

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'enclosure' => '"',
            'escape' => '\\',
            'inputEncoding' => 'UTF-8',
        ];
    }

    public function model(array $row)
    {
        $this->importedCount++;

        $dataNascita = !empty($row['data_nascita']) ? Carbon::parse($row['data_nascita'])->format('Y-m-d') : null;

        return new RuiAccessoris([
            'numero_iscrizione_e' => $row['numero_iscrizione_e'] ?? '',
            'ragione_sociale' => $row['ragione_sociale'] ?? '',
            'cognome_nome' => $row['cognome_nome'] ?? '',
            'sede_legale' => $row['sede_legale'] ?? '',
            'data_nascita' => $dataNascita,
            'luogo_nascita' => $row['luogo_nascita'] ?? '',
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
