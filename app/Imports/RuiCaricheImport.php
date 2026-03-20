<?php

namespace App\Imports;

use App\Models\RuiCariche;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RuiCaricheImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithCustomCsvSettings
{
    protected $importedCount = 0;
    protected $limit = 99999999;
    protected $debug = false;
    protected $fileName = '';

    public function __construct($limit = 99999999, $debug = false, $fileName = '')
    {
        $this->limit = $limit;
        $this->debug = $debug;
        $this->fileName = $fileName;
    }

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
        // Stop if we've reached the limit
        if ($this->importedCount >= $this->limit) {
            return null;
        }

        if ($this->debug && $this->importedCount % 1000 === 0) {
            $fileNameDisplay = $this->fileName ? " ({$this->fileName})" : '';
            echo "📊 Processed {$this->importedCount} records{$fileNameDisplay}...\n";
        }

        $this->importedCount++;

        return new RuiCariche([
            'oss' => $row['oss'] ?? '',
            'numero_iscrizione_rui_pf' => $row['numero_iscrizione_rui_pf'] ?? '',
            'numero_iscrizione_rui_pg' => $row['numero_iscrizione_rui_pg'] ?? '',
            'qualifica_intermediario' => $row['qualifica_intermediario'] ?? '',
            'responsabile' => $row['responsabile'] ?? '',
            'pf_name' => $row['pf_name'] ?? '',
            'pg_name' => $row['pg_name'] ?? '',
            'created_at' => now(),
            'updated_at' => now(),
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
