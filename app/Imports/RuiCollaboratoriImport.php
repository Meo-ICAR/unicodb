<?php

namespace App\Imports;

use App\Models\RuiCollaboratori;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RuiCollaboratoriImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithCustomCsvSettings
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

        return new RuiCollaboratori([
            'oss' => $row['oss'] ?? '',
            'livello' => $row['livello'] ?? '',
            'num_iscr_intermediario' => $row['num_iscr_intermediario'] ?? '',
            'num_iscr_collaboratori_i_liv' => $row['num_iscr_collaboratori_i_liv'] ?? '',
            'num_iscr_collaboratori_ii_liv' => $row['num_iscr_collaboratori_ii_liv'] ?? '',
            'qualifica_rapporto' => $row['qualifica_rapporto'] ?? '',
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
