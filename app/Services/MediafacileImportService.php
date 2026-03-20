<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Client;
use App\Models\ClientPractice;
use App\Models\Company;
use App\Models\Practice;
use App\Models\PracticeCommission;
use App\Models\PracticeScope;
use App\Models\PracticeStatus;
use App\Models\Principal;
use App\Models\SoftwareApplication;
use App\Models\SoftwareMapping;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class MediafacileImportService
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $softwareId;
    protected string $companyId;

    public function __construct()
    {
        // Meglio usare config() invece di env() direttamente nel codice
        $this->apiUrl = config('services.mediafacile.url', env('MEDIAFACILE_BASE_URL', 'https://races.mediafacile.it/ws/hassisto.php'));
        $this->apiKey = config('services.mediafacile.key', env('MEDIAFACILE_HEADER_KEY'));
        $this->companyId = Company::where('oam_name', 'RACES FINANCE SRL')->first()->id;

        $this->softwareId = SoftwareApplication::where('name', 'Mediafacile')->first()->id;
    }

    /**
     * Esegue l'importazione delle pratiche
     */
    public function import(Carbon $startDate, Carbon $endDate): array
    {
        $result = [
            'imported' => 0,
            'updated' => 0,
            'errors' => 0,
            'success' => true,
            'message' => 'Importazione completata'
        ];

        try {
            $data = $this->fetchData($startDate, $endDate);
            //  Log::warning('Skipping not owned item: ' . json_encode($data));
            if (empty($data)) {
                $result['message'] = 'Nessun record trovato nel range di date specificato.';
                return $result;
            }

            foreach ($data as $item) {
                try {
                    $praticaData = $this->mapApiToModel($item);
                    if ($praticaData['is_notowned']) {
                        //   Log::warning('Skipping not owned item: ' . json_encode($item));
                        continue;
                    }

                    if (empty($praticaData['id'])) {
                        Log::warning('Skipping item without id: ' . json_encode($item));
                        $result['errors']++;
                        continue;
                    }

                    $this->processRecord($praticaData);

                    // Se non ha lanciato eccezioni, consideriamo il record processato
                    // Un modo semplice per sapere se è stato aggiornato o creato:
                    if (Practice::where('CRM_code', $praticaData['CRM_code'])->exists()) {
                        $result['updated']++;  // Nota: questo conteggio è approssimativo se fai updateOrCreate
                    } else {
                        $result['imported']++;
                    }
                } catch (Exception $e) {
                    Log::error('Error processing item: ' . $e->getMessage(), ['item' => $item]);
                    $result['errors']++;
                }
            }
        } catch (Exception $e) {
            Log::error("Errore critico durante l'importazione Pratiche: " . $e->getMessage());
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Recupera e formatta i dati dall'API
     */
    protected function fetchData(Carbon $startDate, Carbon $endDate): array
    {
        $queryParams = [
            'table' => 'pratiche',
            'data_inizio' => $startDate->format('Y-m-d'),
            'data_fine' => $endDate->format('Y-m-d'),
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json, */*',
            'User-Agent' => 'Compilio Import-Pratiche/1.0',
            'X-Api-Key' => $this->apiKey,
        ])
            ->timeout(60)
            ->connectTimeout(10)
            ->withOptions(['http_errors' => false, 'verify' => false])
            ->retry(3, 1000, function ($exception) {
                return $exception instanceof \Illuminate\Http\Client\ConnectionException ||
                    ($exception->getCode() >= 500);
            })
            ->get($this->apiUrl, $queryParams);

        if (!$response->successful()) {
            throw new Exception('API request failed with status: ' . $response->status());
        }

        return $this->parseTsv($response->body());
    }

    /**
     * Converte il body TSV in un array associativo
     */
    protected function parseTsv(string $body): array
    {
        $lines = array_values(array_filter(explode("\n", trim($body)), function ($line) {
            return trim($line) !== '';
        }));

        if (empty($lines))
            return [];

        $headers = $this->parseLine($lines[0]);

        $data = [];

        for ($i = 1; $i < count($lines); $i++) {
            $values = $this->parseLine($lines[$i]);
            if (count($values) === count($headers)) {
                $data[] = array_combine($headers, $values);
            }
        }

        return $data;
    }

    protected function parseLine(string $line): array
    {
        return array_map('trim', explode("\t", $line));
    }

    /**
     * Salva o aggiorna il record a DB
     */
    protected function processRecord(array $praticaData): void
    {
        $tipoProdotto = strtolower($praticaData['tipo_prodotto']);
        $practiceSwType = SoftwareMapping::firstOrCreate(
            ['software_application_id' => $this->softwareId, 'mapping_type' => 'PRACTICE_TYPE', 'external_value' => $tipoProdotto],
            [
                'name' => $tipoProdotto,
                'internal_id' => 0,  // ID, da mappare correttamente in base alla logica di business
                'description' => 'Mapping automatico da Mediafacile',
            ]
        );
        if ($practiceSwType->internal_id === 0) {
            $practiceScope = PracticeScope::create([
                'name' => $tipoProdotto,
                'code' => 'Mediafacile',
                //  'description' => 'Mapping automatico da Mediafacile',
            ]);
            $practiceSwType->internal_id = $practiceScope->id;
            $practiceSwType->save();
        }
        $praticaData['practice_scope_id'] = $practiceSwType->internal_id;

        $statoPratica = strtolower($praticaData['stato_pratica']);
        $status = SoftwareMapping::firstOrCreate(
            ['software_application_id' => $this->softwareId, 'mapping_type' => 'PRACTICE_STATUS', 'external_value' => $statoPratica],
            [
                'name' => $statoPratica,
                'internal_id' => 0,  // Default ID, da mappare correttamente in base alla logica di business
                'description' => 'Mapping automatico da Mediafacile',
            ]
        );
        if ($status->internal_id === 0) {
            $practiceStatus = PracticeStatus::create([
                'name' => $statoPratica,
                'code' => 'Mediafacile',
            ]);
            $status->internal_id = $practiceStatus->id;
            $status->save();
        }
        $praticaData['practice_status_id'] = $status->internal_id;

        $existing = Practice::where('CRM_code', $praticaData['CRM_code'])->first();

        // Gestione cliente
        $client = null;
        if (!empty($praticaData['codice_fiscale'])) {
            $client = Client::firstOrCreate(
                ['tax_code' => $praticaData['codice_fiscale'], 'company_id' => $this->companyId],
                [
                    'first_name' => $praticaData['nome_cliente'],
                    'name' => $praticaData['cognome_cliente'],
                    'company_id' => $this->companyId,
                ]
            );
            $praticaData['client_id'] = $client->id;
        }
        if (!empty($client)) {
            if ($existing) {
                $existing->update($praticaData);
            } else {
                $praticaData['company_id'] = $this->companyId;
                // Software Mapping

                // Gestione agente
                if (!empty($praticaData['partita_iva_agente'])) {
                    $agent = Agent::firstOrCreate(
                        ['vat_number' => $praticaData['partita_iva_agente'], 'company_id' => $this->companyId],
                        [
                            'name' => $praticaData['denominazione_agente'],
                            'company_id' => $this->companyId,
                            'vat_number' => $praticaData['partita_iva_agente'],
                        ]
                    );
                    $praticaData['agent_id'] = $agent->id;
                }

                // Gestione della banca
                if (!empty($praticaData['denominazione_banca'])) {
                    $principal = Principal::firstOrCreate(
                        ['name' => $praticaData['denominazione_banca'], 'company_id' => $this->companyId],
                        [
                            'ivass_section' => $praticaData['tipo_prodotto'] == 'Polizza' ? 'B' : null,
                        ]
                    );
                    $praticaData['principal_id'] = $principal->id;
                }

                $rateData = $this->parseDescription($praticaData['denominazione_prodotto']);
                $praticaData['amount'] = $rateData['rata'] * $rateData['nrate'];
                //  $praticaData['na'] = $rateData['nrate'];
                $praticaData['name'] = $praticaData['denominazione_prodotto'];

                $existing = Practice::create($praticaData);
                ClientPractice::create([
                    'company_id' => $this->companyId,
                    'client_id' => $client->id,
                    'practice_id' => $existing->id,
                ]);
            }
        }

        // Check if existing practice exists before calling methods
        if ($existing) {
            $practiceStatus = PracticeStatus::where('code', $existing->stato_pratica)->first();
            if ($practiceStatus) {
                $existing->update(['status' => $practiceStatus->status,
                    'practice_status_id' => $practiceStatus->id]);
                if ($practiceStatus->is_rejected && !$existing->isPerfectedStatus() && empty($existing->rejected_at)) {
                    // TODO: inviare email al cliente
                    $existing->update(['rejected_at' => $existing->inserted_at->addMonth(), 'status' => 'rejected']);
                }
                $existing->save();
            }
        }
    }

    /**
     * Mappa i dati grezzi dell'API nel formato del Model
     */
    protected function mapApiToModel(array $apiData): array
    {
        $dataInserimento = $this->parseDate($apiData['Data Inserimento Pratica'] ?? null);
        //  $this->info($apiData['Data Inserimento Pratica'] . ' Data inserimento: ' . $dataInserimento);
        $sendedAt = $this->parseDate($apiData['Data_invio_istruttoria'] ?? null);
        //   $this->info($apiData['Data_invio_istruttoria'] . ' Sended at: ' . $sendedAt);
        $approvedAt = $this->parseDate($apiData['Data_delibera_banca'] ?? null);
        $erogatedAt = $this->parseDate($apiData['Data_erogazione'] ?? null);
        if (!empty($erogatedAt)) {
            // $erogatedAt = $approvedAt;
            // Log::warning('Erogated item: ' . json_encode($apiData));
        }

        $amount = $this->parseDecimal($apiData['Montante'] ?? null);
        $net = $this->parseDecimal($apiData['Importo_Erogazione'] ?? null);

        return [
            'id' => $apiData['ID Pratica'] ?? (string) Str::uuid(),
            'CRM_code' => $apiData['ID Pratica'] ?? null,
            'nome_cliente' => $apiData['Cognome Cliente'] ?? null,
            'cognome_cliente' => $apiData['Nome Cliente'] ?? null,
            'codice_fiscale' => $apiData['Codice Fiscale'] ?? null,
            'denominazione_agente' => $apiData['Denominazione Agente'] ?? null,
            'partita_iva_agente' => $apiData['Partita IVA Agente'],
            'denominazione_banca' => $apiData['Denominazione Banca'] ?? null,
            'tipo_prodotto' => $apiData['Tipo Prodotto'] ?? null,
            'denominazione_prodotto' => $apiData['Descrizione Prodotto'] ?? null,
            'inserted_at' => $dataInserimento ?? now(),
            'stato_pratica' => $apiData['Stato Pratica'] ?? null,
            'sended_at' => $sendedAt,
            'approved_at' => $approvedAt,
            'erogated_at' => $erogatedAt,
            'amount' => $amount,
            'net' => $net,
            'is_notowned' => ($apiData['Pratica_terzi'] ?? '') == 'SI' ? true : false,
        ];
    }

    private function parseDecimal($decimalValue)
    {
        if (empty($decimalValue) || trim($decimalValue) === '') {
            return null;
        }

        // Remove dots and commas, then handle decimal separator
        $cleaned = str_replace(['.', ','], ['', '.'], trim($decimalValue));

        if (is_numeric($cleaned)) {
            return (float) $cleaned;
        }

        return null;
    }

    private function parseDate($dateValue)
    {
        if (empty($dateValue) || trim($dateValue) === '') {
            return null;
        }

        try {
            // Try different date formats
            $formats = ['d/m/Y', 'Y-m-d', 'd/m/Y H:i:s', 'Y-m-d H:i:s'];

            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, trim($dateValue));
                } catch (\Exception $e) {
                    // Continue to next format
                }
            }

            // If no format works, try Carbon's flexible parsing
            return new Carbon($dateValue);
        } catch (\Exception $e) {
            $this->warn('Failed to parse date: ' . $dateValue);
            return null;
        }
    }

    private function parseDate2($dateValue)
    {
        if (empty($dateValue) || trim($dateValue) === '') {
            return null;
        }

        try {
            // Try different date formats
            $formats = ['m/d/Y', 'Y-m-d', 'd/m/Y H:i:s', 'Y-m-d H:i:s'];

            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, trim($dateValue));
                } catch (\Exception $e) {
                    // Continue to next format
                }
            }

            // If no format works, try Carbon's flexible parsing
            return new Carbon($dateValue);
        } catch (\Exception $e) {
            $this->warn('Failed to parse date: ' . $dateValue);
            return null;
        }
    }

    private function parseDescription($description)
    {
        if (empty($description)) {
            return null;
        }

        // Pattern: "TipoProdotto - Banca - Rata x NRate (Codice)"
        // Esempi:
        // "Cessione - CAPITALFIN - 158 x 120 (QT06447)"
        // "Prestito - AGOS SPA - 155,36 x 60 (QT06446)"
        // "Delega -  - 100 x 84 (QT06438)"
        // "Mutuo - ING BANK - PRS - 599 x  (QT06440)"

        // Rimuovi il codice finale tra parentesi
        $description = preg_replace('/\s*\([^)]*\)\s*$/', '', $description);

        // Dividi per " - "
        $parts = explode(' - ', $description);

        $tipo_prodotto = trim($parts[0] ?? '');
        $denominazione_banca = '';
        $rata = null;
        $nrate = null;

        // Cerca la banca e i dati finanziari
        $remaining_parts = array_slice($parts, 1);

        foreach ($remaining_parts as $part) {
            $part = trim($part);

            // Se contiene "x" probabilmente è la parte finanziaria
            if (preg_match('/(\d+(?:,\d+)?)\s*x\s*(\d*)/', $part, $matches)) {
                $rata = str_replace(',', '.', $matches[1]);
                $nrate = !empty($matches[2]) ? (int) $matches[2] : null;
            } elseif (!empty($part) && !preg_match('/\d+\s*x/', $part)) {
                // È probabilmente il nome della banca
                $denominazione_banca = $part;
            }
        }

        // Se non abbiamo trovato la banca, prova a estrarla dal pattern completo
        if (empty($denominazione_banca) && preg_match('/^[^-]+-\s*([^-]+)-/', $description, $matches)) {
            $denominazione_banca = trim($matches[1]);
        }

        return [
            'tipo_prodotto' => $tipo_prodotto,
            'denominazione_banca' => $denominazione_banca,
            'rata' => $rata,
            'nrate' => $nrate,
        ];
    }
}
