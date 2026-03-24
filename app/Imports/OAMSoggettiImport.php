<?php

namespace App\Imports;

use App\Models\OAMSoggetti;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithStartRow;

class OAMSoggettiImport implements ToModel, WithStartRow, WithChunkReading, WithCustomCsvSettings
{
    protected $importedCount = 0;
    protected $limit = 99999999;  // Limit to 99999999 records for production
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
            'escape_character' => '\\',
            'input_encoding' => 'UTF-8',
        ];
    }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function model(array $row): ?OAMSoggetti
    {
        // Skip empty rows
        if (empty($row[0])) {
            if ($this->debug) {
                echo "Skipping empty row {$this->importedCount}\n";
            }
            return null;
        }

        // Convert boolean values
        $autorizzatoAdOperare = $this->parseBoolean($row[1] ?? '');
        $numeroCollaborazioniAttive = $this->parseInteger($row[13] ?? '0');

        $this->importedCount++;

        if ($this->debug && $this->importedCount % 10 === 0) {
            echo "Processed {$this->importedCount} records...\n";
        }

        if ($this->debug) {
            echo 'Creating model for row: ' . json_encode($row) . "\n";
        }

        try {
            $model = new OAMSoggetti([
                'denominazione_sociale' => $this->cleanString($row[0] ?? ''),
                'autorizzato_ad_operare' => $autorizzatoAdOperare,
                'persona' => $this->cleanString($row[2] ?? ''),
                'codice_fiscale' => $this->cleanString($row[3] ?? ''),
                'domicilio_sede_legale' => $this->cleanString($row[4] ?? ''),
                'elenco' => $this->cleanString($row[5] ?? ''),
                'numero_iscrizione' => $this->cleanString($row[6] ?? ''),
                'data_iscrizione' => $this->parseDate($row[7] ?? ''),
                'stato' => $this->cleanString($row[8] ?? ''),
                'data_stato' => $this->parseDate($row[9] ?? ''),
                'causale_stato_note' => $this->cleanString($row[10] ?? ''),
                'check_collaborazione' => $this->cleanString($row[11] ?? ''),
                'dipendente_collaboratore_di' => $this->cleanString($row[12] ?? ''),
                'numero_collaborazioni_attive' => $numeroCollaborazioniAttive,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($this->debug) {
                echo "Model created successfully\n";
            }

            return $model;
        } catch (\Exception $e) {
            if ($this->debug) {
                echo 'Error creating model: ' . $e->getMessage() . "\n";
            }
            Log::error('Error creating OAMSoggetti model: ' . $e->getMessage());
        }

        return null;
    }

    protected function parseBoolean($value): bool
    {
        return in_array(strtolower(trim($value)), ['si', 'sì', 'true', '1', 'yes']);
    }

    protected function parseInteger($value): int
    {
        return (int) str_replace(['.', ','], ['', ''], trim($value ?? '0'));
    }

    protected function parseDate($value): ?\DateTime
    {
        if (empty($value)) {
            return null;
        }

        // Try different date formats
        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y'];

        foreach ($formats as $format) {
            $dateObj = \DateTime::createFromFormat($format, $value);
            if ($dateObj !== false) {
                return $dateObj;
            }
        }

        return null;
    }

    protected function cleanString($value): string
    {
        if (empty($value)) {
            return '';
        }

        // Remove extra quotes and trim
        $value = trim($value, '" \t\n\r\0\x0B');

        // Convert encoding if needed
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8, ISO-8859-1');
        }

        return $value;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
