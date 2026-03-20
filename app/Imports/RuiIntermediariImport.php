<?php

namespace App\Imports;

use App\Models\Rui;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RuiIntermediariImport implements ToCollection, WithHeadingRow, WithChunkReading, WithCustomCsvSettings
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
            'escape' => '\\',
            'inputEncoding' => 'UTF-8',
        ];
    }

    public function collection(Collection $rows)
    {
        if ($this->debug && $this->importedCount % 1000 === 0) {
            $fileNameDisplay = $this->fileName ? " ({$this->fileName})" : '';
            echo "📊 Processing chunk of {$rows->count()} records{$fileNameDisplay}...\n";
        }

        $this->importedCount += $rows->count();

        // 1. Estrai tutti i numeri di iscrizione dal chunk
        $numeriIscrizione = $rows->pluck('numero_iscrizione_rui')->filter()->unique()->toArray();

        if (empty($numeriIscrizione)) {
            return;  // Skip se non ci sono numeri validi
        }

        // 2. Query UNICA per trovare tutti i record esistenti
        $existingRecords = DB::table('rui')
            ->whereIn('numero_iscrizione_rui', $numeriIscrizione)
            ->pluck('numero_iscrizione_rui', 'id')
            ->toArray();

        // 3. Separa nuovi record da aggiornamenti
        $newRecords = [];
        $updateRecords = [];

        foreach ($rows as $row) {
            $numeroIscrizioneRui = $row['numero_iscrizione_rui'] ?? '';

            if (empty($numeroIscrizioneRui)) {
                continue;
            }

            // Skip specific record ID 128575
            if ($numeroIscrizioneRui === 'E000638407') {
                if ($this->debug) {
                    echo "⚠️ Skipping record E000638407 (ID 128575) as requested\n";
                }
                continue;
            }

            // Prepara i dati
            $dataInizioInoperativita = !empty($row['data_inizio_inoperativita']) ? Carbon::createFromFormat('d/m/Y', $row['data_inizio_inoperativita'])->format('Y-m-d') : null;
            $dataIscrizione = !empty($row['data_iscrizione']) ? Carbon::createFromFormat('d/m/Y', $row['data_iscrizione'])->format('Y-m-d') : null;
            $dataNascita = !empty($row['data_nascita']) ? Carbon::createFromFormat('d/m/Y', $row['data_nascita'])->format('Y-m-d') : null;

            $recordData = [
                'numero_iscrizione_rui' => $numeroIscrizioneRui,
                'oss' => $row['oss'] ?? '',
                'inoperativo' => !empty($row['inoperativo']) ? (bool) $row['inoperativo'] : false,
                'data_inizio_inoperativita' => $dataInizioInoperativita,
                'data_iscrizione' => $dataIscrizione,
                'cognome_nome' => $row['cognome_nome'] ?? '',
                'stato' => $row['stato'] ?? '',
                'comune_nascita' => $row['comune_nascita'] ?? '',
                'data_nascita' => $dataNascita,
                'ragione_sociale' => $row['ragione_sociale'] ?? '',
                'provincia_nascita' => $row['provincia_nascita'] ?? '',
                'titolo_individuale_sez_a' => $row['titolo_individuale_sez_a'] ?? '',
                'attivita_esercitata_sez_a' => $row['attivita_esercitata_sez_a'] ?? '',
                'titolo_individuale_sez_b' => $row['titolo_individuale_sez_b'] ?? '',
                'attivita_esercitata_sez_b' => $row['attivita_esercitata_sez_b'] ?? '',
                'updated_at' => now(),
            ];

            if (in_array($numeroIscrizioneRui, $existingRecords)) {
                // Record esistente - aggiungi agli aggiornamenti
                $updateRecords[] = $recordData;
            } else {
                // Nuovo record - aggiungi timestamp di creazione
                $recordData['created_at'] = now();
                $newRecords[] = $recordData;
            }
        }

        // 4. Inserimenti in massa (BULK INSERT) - MOLTO VELOCE
        if (!empty($newRecords)) {
            // Disabilita temporaneamente i vincoli per velocità
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            try {
                // Inserisci in chunk di 1000 per non sovraccaricare la memoria
                $chunks = array_chunk($newRecords, 1000);
                foreach ($chunks as $chunk) {
                    DB::table('rui')->insert($chunk);
                }

                if ($this->debug) {
                    echo ' Inserted ' . count($newRecords) . " new records\n";
                }
            } finally {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
        }

        // 5. Aggiornamenti in massa (BULK UPDATE)
        if (!empty($updateRecords)) {
            // Usa UPDATE CASE WHEN per aggiornamenti in massa
            $this->bulkUpdate($updateRecords);

            if ($this->debug) {
                echo ' Updated ' . count($updateRecords) . " existing records\n";
            }
        }
    }

    /**
     * Aggiornamento in massa usando CASE WHEN - molto più veloce di update() singoli
     */
    private function bulkUpdate(array $records): void
    {
        if (empty($records)) {
            return;
        }

        // Prepara i CASE statements per ogni campo
        $cases = [];
        $numeroIscrizioni = [];

        foreach ($records as $record) {
            $numero = $record['numero_iscrizione_rui'];
            $numeroIscrizioni[] = $numero;

            foreach (['oss', 'inoperativo', 'data_inizio_inoperativita', 'data_iscrizione', 'cognome_nome',
                    'stato', 'comune_nascita', 'data_nascita', 'ragione_sociale', 'provincia_nascita',
                    'titolo_individuale_sez_a', 'attivita_esercitata_sez_a', 'titolo_individuale_sez_b',
                    'attivita_esercitata_sez_b'] as $field) {
                if (array_key_exists($field, $record)) {
                    $value = is_null($record[$field]) ? 'NULL' : (is_bool($record[$field]) ? ($record[$field] ? '1' : '0') : "'{$record[$field]}'");
                    $cases[$field][] = "WHEN '{$numero}' THEN {$value}";
                }
            }
        }

        // Costruisci la query di aggiornamento
        $updateSql = 'UPDATE rui SET ';
        $setClauses = [];

        foreach ($cases as $field => $whenClauses) {
            $setClauses[] = "{$field} = CASE numero_iscrizione_rui " . implode(' ', $whenClauses) . " ELSE {$field} END";
        }

        $setClauses[] = 'updated_at = NOW()';
        $updateSql .= implode(', ', $setClauses);
        $updateSql .= " WHERE numero_iscrizione_rui IN ('" . implode("','", $numeroIscrizioni) . "')";

        // Esegui la query di aggiornamento
        DB::statement($updateSql);
    }

    public function model(array $row)
    {
        // Non usato con ToCollection
        return null;
    }

    public function batchSize(): int
    {
        return 5000;  // Batch più grandi per massima velocità
    }

    public function chunkSize(): int
    {
        return 10000;  // Chunk più grandi per ridurre le query
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }
}
