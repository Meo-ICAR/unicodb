<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Client;
use App\Models\Company;
use App\Models\Practice;
use App\Models\PracticeCommission;
use App\Models\PracticeCommissionStatus;
use App\Models\Principal;
use App\Models\SoftwareApplication;
use App\Models\SoftwareMapping;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class MediafacileProvvigioniService
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

            if (empty($data)) {
                $result['message'] = 'Nessun record trovato nel range di date specificato.';
                return $result;
            }

            foreach ($data as $item) {
                try {
                    $provvigioneData = $this->mapApiToModel($item);

                    if (empty($provvigioneData['amount']) || $provvigioneData['amount'] == 0) {
                        Log::warning('Skipping item without Importo: ' . json_encode($item));
                        $result['errors']++;
                        continue;
                    }
                    if (empty($provvigioneData['ID Compenso'])) {
                        Log::warning('Skipping item without id: ' . json_encode($item));
                        $result['errors']++;
                        continue;
                    }
                    $this->processRecord($provvigioneData);

                    // Se non ha lanciato eccezioni, consideriamo il record processato
                    // Un modo semplice per sapere se è stato aggiornato o creato:
                    if (PracticeCommission::where('CRM_code', $provvigioneData['ID Compenso'])->exists()) {
                        $result['updated']++;  // Nota: questo conteggio è approssimativo se fai updateOrCreate
                    } else {
                        $result['imported']++;
                    }
                } catch (Exception $e) {
                    Log::error('Error processing item: ' . $e->getMessage(), ['item' => $item]);
                    $result['errors']++;
                }
            }
            // Update practices perfected_at based on commission data
            DB::statement("
                INSERT IGNORE INTO principal_scopes (
    principal_id,
    tipo_prodotto,
    start_date,
    is_active,
    is_forced,
    name
)
SELECT
    x.principal_id,
    x.tipo_prodotto,
    MIN(p.inserted_at) as start_date,
    1 as is_active, -- Impostiamo a 1 (attivo) di default
    0 as is_forced, -- Impostiamo a 0 (non forzato) di default
    CONCAT('Ambito ', x.tipo_prodotto) as name -- Generiamo un nome basato sul tipo prodotto
FROM practice_commissions p
INNER JOIN practices x ON x.id = p.practice_id
WHERE p.tipo <> 'Cliente'
  AND x.principal_id IS NOT NULL
  AND x.tipo_prodotto IS NOT NULL
  AND x.company_id = ?
GROUP BY x.principal_id, x.tipo_prodotto
ORDER BY x.principal_id, x.tipo_prodotto;
            ", [$this->companyId]);

            // Mark all principals as dummy first
            DB::statement('UPDATE principals p set p.is_dummy = true');

            // Update principals that have commissions to not dummy
            DB::statement('
                UPDATE principals p
                JOIN (
                    SELECT principal_id
                    FROM practice_commissions
                    GROUP BY principal_id
                    HAVING SUM(amount) > 0
                ) c_sum ON p.id = c_sum.principal_id
                SET p.is_dummy = false
            ');
            DB::statement('
                UPDATE practices p
                INNER JOIN (
                    SELECT
                        c.practice_id,
                        MAX(c.invoice_at) as max_invoice_at,
                        SUM(c.amount) as total_amount
                    FROM practice_commissions c

                    GROUP BY c.practice_id
                ) c ON p.id = c.practice_id
                SET p.invoice_at = c.max_invoice_at, p.brokerage_fee = c.total_amount
            ');
            // Delete dummy principals
            DB::statement('DELETE FROM principals WHERE is_dummy = true');

            // Step 1: Delete all existing practice_oam records for the company
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
            'table' => 'compensi',
            'data_inizio' => $startDate->format('Y-m-d'),
            'data_fine' => $endDate->format('Y-m-d'),
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json, */*',
            'User-Agent' => 'Compilio Import-Provvigioni/1.0',
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
    protected function processRecord(array $provvigioneData): void
    {
        //    \Log::info('Processing provvigione record', $provvigioneData);
        $statusPayment = strtolower($provvigioneData['status_payment']);
        $commissionStatus = SoftwareMapping::firstOrCreate(
            ['software_application_id' => $this->softwareId, 'mapping_type' => 'COMMISSION_STATUS', 'external_value' => $statusPayment],
            [
                'name' => $statusPayment,
                'description' => 'Mapping automatico da Mediafacile',
                'internal_id' => 0,  // Default ID, da mappare correttamente in base al valore
            ]
        );
        if ($commissionStatus->internal_id === 0) {
            $practiceCommissionStatus = PracticeCommissionStatus::create([
                'name' => $statusPayment,
                'code' => 'Mediafacile',
                //  'description' => 'Mapping automatico da Mediafacile',
            ]);
            $commissionStatus->internal_id = $practiceCommissionStatus->id;
            $commissionStatus->save();
        }
        $provvigioneData['practice_commission_status_id'] = $commissionStatus->internal_id;

        $practice = Practice::firstOrCreate(
            ['company_id' => $this->companyId, 'CRM_code' => $provvigioneData['id_pratica']],
            [
                'name' => 'Mapping automatico da Mediafacile',
            ]
        );

        $existing = PracticeCommission::where('CRM_code', $provvigioneData['CRM_code'])->first();

        if ($existing) {
            $existing->update($provvigioneData);
        } else {
            $provvigioneData['company_id'] = $this->companyId;

            if ($existing) {
                $existing->update($provvigioneData);
            } else {
                $provvigioneData['company_id'] = $this->companyId;
                // Software Mapping

                $provvigioneData['practice_id'] = $practice->id;

                //   $provvigioneData['practice_scope_id'] = $practiceType->internal_id;

                /*
                 * $status = SoftwareMapping::firstOrCreate(
                 *     ['software_application_id' => $this->softwareId, 'mapping_type' => 'PRACTICE_STATUS', 'external_value' => $provvigioneData['stato_pratica']],
                 *     [
                 *         'name' => $provvigioneData['stato_pratica'],
                 *         'internal_id' => 1,  // Default ID, da mappare correttamente in base alla logica di business
                 *         'description' => 'Mapping automatico da Mediafacile',
                 *     ]
                 * );
                 * $provvigioneData['status_id'] = $status->internal_id;
                 *
                 *
                 *  * // Gestione cliente
                 *  * $client = Client::firstOrCreate(
                 *  *     ['tax_code' => $provvigioneData['codice_fiscale'], 'company_id' => $this->companyId],
                 *  *     [
                 *  *         'first_name' => $provvigioneData['nome_cliente'],
                 *  *         'name' => $provvigioneData['cognome_cliente'],
                 *  *         'company_id' => $this->companyId,
                 *  *     ]
                 *  * );
                 *  * $provvigioneData['client_id'] = $client->id;
                 */

                // Gestione agente
                $agent = Agent::firstOrCreate(
                    ['vat_number' => $provvigioneData['piva'], 'company_id' => $this->companyId],
                    [
                        'name' => $provvigioneData['denominazione_riferimento'],
                        'company_id' => $this->companyId,
                    ]
                );
                $provvigioneData['agent_id'] = $agent->id;

                // Gestione della banca
                $principal = Principal::firstOrCreate(
                    ['name' => $provvigioneData['istituto_finanziario'], 'company_id' => $this->companyId],
                    [
                        // altri campi se necessari
                    ]
                );
                $provvigioneData['principal_id'] = $principal->id;

                $provvigioneData['name'] = $provvigioneData['descrizione'];

                $provvigioneData['is_insurance'] = false;

                $existing = PracticeCommission::create($provvigioneData);
            }
        }

        /*
         * \Log::info('Update pratica status', [
         *     'practice_id' => $practice->id,
         *     'practice_commission_id' => $practiceCommission->id,
         *     'is_perfected' => $practiceCommission->isPerfected(),
         *     'is_practice_perfected' => $practice->isPerfectedStatus(),
         * ]);
         */
        if ($existing) {
            if (!empty($existing->invoice_at)) {
                $existing->update(['paided_at' => $existing->invoice_at]);
                if (empty($existing->perfected_at)) {
                    $existing->update(['perfected_at' => $existing->invoice_at]);
                }
            }
            if ($existing->isPerfectedStatus()) {
                if (!$practice->isPerfectedStatus()) {
                    $practice->update(['perfected_at' => $existing->status_at, 'status' => 'perfected']);
                }
                if (empty($existing->perfected_at)) {
                    $existing->update(['perfected_at' => $existing->status_at]);
                }
            }
        }
    }

    protected function mapApiToModel(array $apiData): array
    {
        // Helper function to parse dates from API
        $parseDate = function ($dateValue) {
            if (empty($dateValue))
                return null;

            try {
                // Handle DD/MM/YYYY format
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dateValue)) {
                    return Carbon::createFromFormat('d/m/Y', $dateValue)->startOfDay();
                }
                // Handle other date formats
                if (strtotime($dateValue)) {
                    return Carbon::parse($dateValue);
                }
            } catch (\Exception $e) {
                $this->warn("Failed to parse date '" . $dateValue . "': " . $e->getMessage());
            }
            return null;
        };

        // Check if required fields exist in API data
        $missingFields = [];
        foreach (['ID Compenso', 'Data Inserimento', 'Descrizione', 'Tipo', 'Importo'] as $field) {
            if (!array_key_exists($field, $apiData) || empty($apiData[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            Log::warning('Missing required fields: ' . implode(', ', $missingFields) . ' - Item: ' . json_encode($apiData));
            return [];
        }

        $requiredFields = [
            'ID Compenso',
            'Data Inserimento',
            'Descrizione',
            'Tipo',
            'Importo',
            'Importo Effettivo',
            'Stato',
            'Data Pagamento',
            'N. Fattura',
            'Data Fattura',
            'Data Status',
            'Status Compenso',
            'Denominazione Riferimento',
            'Entrata Uscita',
            'ID Pratica',
            'Agente',
            'Istituto finanziario',
            'Partita IVA Agente',
            'Codice Fiscale Agente',
        ];

        // Parse all date fields
        $dataInserimento = $parseDate($apiData['Data Inserimento'] ?? null);
        $dataPagamento = $parseDate($apiData['Data Pagamento'] ?? null);
        $dataFattura = $parseDate($apiData['Data Fattura'] ?? null);
        $dataStatus = $parseDate($apiData['Data Status'] ?? null);

        return [
            'ID Compenso' => $apiData['ID Compenso'] ?? null,
            'CRM_code' => $apiData['ID Compenso'] ?? null,
            'inserted_at' => $dataInserimento ? $dataInserimento->toDateTimeString() : null,
            'descrizione' => $apiData['Descrizione'] ?? null,
            'tipo' => $apiData['Tipo'] ?? 'provvigione',
            'amount' => is_numeric($apiData['Importo']) ? $apiData['Importo'] : (is_string($apiData['Importo']) ? (float) str_replace(',', '.', $apiData['Importo']) : 0),
            'status_payment' => $apiData['Stato'] ?? '',
            'paided_at' => $dataPagamento ? $dataPagamento->toDateTimeString() : null,
            'invoice_number' => $apiData['N. Fattura'] ?? null,
            'invoice_at' => $dataFattura ? $dataFattura->toDateTimeString() : null,
            'status_at' => $dataStatus ? $dataStatus->toDateTimeString() : null,
            'status_payment' => $apiData['Status Compenso'] ?? null,
            'is_payment' => $apiData['Entrata Uscita'] === 'Uscita' ? true : false,
            'id_pratica' => $apiData['ID Pratica'] ?? null,
            'segnalatore' => $apiData['Agente'] ?? null,
            'istituto_finanziario' => $apiData['Istituto finanziario'] ?? null,
            'denominazione_riferimento' => $apiData['Denominazione Riferimento'] ?? null,
            // agente sulla provvigione (Uscita = pagamento, Entrata = ricevuta)
            'piva' => !empty($apiData['Partita IVA Agente'])
                ? $apiData['Partita IVA Agente']
                : (!empty($apiData['Codice Fiscale Agente']) ? $apiData['Codice Fiscale Agente'] : null),
            'cf' => $apiData['Codice Fiscale Agente'] ?? null,
            // 'annullato' => !empty($apiData['ANNULLATA']) && $apiData['ANNULLATA'] === 'SI',
            //  'invoice_number' => $apiData['ANNULLATA'] ?? null,
            'fonte' => 'mediafacile',
            'is_coordination' => $apiData['Agente'] <> $apiData['Denominazione Riferimento'],
            'is_client' => (isset($apiData['Descrizione']) && str_contains($apiData['Descrizione'], 'liente')),
        ];
    }
}
