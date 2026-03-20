<?php

namespace App\Imports;

use App\Models\RuiSedi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RuiSediImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithCustomCsvSettings
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

        return new RuiSedi([
            'oss' => $row['oss'] ?? '',
            'numero_iscrizione_int' => $row['numero_iscrizione_int'] ?? '',
            'tipo_sede' => $row['tipo_sede'] ?? '',
            'comune_sede' => $row['comune_sede'] ?? '',
            'provincia_sede' => $row['provincia_sede'] ?? '',
            'cap_sede' => $row['cap_sede'] ?? '',
            'indirizzo_sede' => $row['indirizzo_sede'] ?? '',
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
