<?php

namespace Database\Seeders;

use App\Models\Audit;
use App\Models\AuditItem;
use App\Models\BusinessFunction;  // Importo il modello delle funzioni
use App\Models\OamScope;
use App\Models\Remediation;
use Illuminate\Database\Seeder;

class RemediationSeeder extends Seeder
{
    public function run()
    {
        // $oamScopeId = OamScope::firstOrCreate(['name' => 'OAM'])->id;

        $audit = Audit::firstOrCreate([
            'title' => 'Audit Interno OAM',
            'start_date' => now(),
        ]);

        // Recupero il primo audit item esistente
        $auditItem = AuditItem::first();

        if (!$auditItem) {
            // Get a valid business function or use null
            $businessFunction = BusinessFunction::first();

            // Se non esiste, ne creo uno
            $auditItem = AuditItem::create([
                'audit_id' => $audit->id,
                'auditable_type' => 'App\Models\Practice',
                'auditable_id' => '1',
                'business_function_id' => $businessFunction?->id,  // Use valid ID or null
                'name' => 'Audit Item di esempio',
                'finding_description' => 'Audit item per remediation',
                'result' => 'OK',
            ]);
        }

        // Recupero le funzioni tramite il loro "code" univoco
        $funcAml = BusinessFunction::where('code', 'CTRL-AML')->first();
        $funcRete = BusinessFunction::where('code', 'BUS-RETE')->first();
        $funcDpo = BusinessFunction::where('code', 'CTRL-DPO')->first();
        $funcReclami = BusinessFunction::where('code', 'SUP-RECLAMI')->first();
        $funcGov = BusinessFunction::where('code', 'GOV-CDA')->first();

        $azioniDiRimedio = [
            [
                'function_id' => $funcAml?->id,  // Assegnato all'Ufficio Antiriciclaggio
                'remediation_type' => 'AML',
                'name' => 'Segnalazione Operazione Sospetta (SOS)',
                'description' => 'Predisposizione e invio immediato della SOS alla UIF...',
                'timeframe_hours' => 24,
                'timeframe_desc' => 'Immediato (max 24 ore)',
            ],
            [
                'function_id' => $funcRete?->id,  // Assegnato alla Gestione Rete
                'remediation_type' => 'Monitoraggio Rete',
                'name' => 'Sospensione cautelare collaboratore',
                'description' => 'Blocco immediato delle credenziali di accesso al gestionale...',
                'timeframe_hours' => 48,
                'timeframe_desc' => 'Entro 48 ore',
            ],
            [
                'function_id' => $funcDpo?->id,  // Assegnato al DPO
                'remediation_type' => 'Privacy',
                'name' => 'Notifica Data Breach',
                'description' => 'Raccolta delle informazioni sulla violazione dei dati...',
                'timeframe_hours' => 72,
                'timeframe_desc' => 'Entro 72 ore',
            ],
            [
                'function_id' => $funcAml?->id,
                'remediation_type' => 'AML',
                'name' => 'Integrazione documentazione per Adeguata Verifica',
                'description' => 'Contatto con il cliente per richiedere documenti mancanti...',
                'timeframe_hours' => 168,
                'timeframe_desc' => 'Entro 7 giorni',
            ],
            [
                'function_id' => $funcReclami?->id,  // Assegnato Gestione Reclami
                'remediation_type' => 'Gestione Reclami',
                'name' => 'Risoluzione e riscontro reclamo',
                'description' => 'Redazione formale della lettera di risposta al reclamo...',
                'timeframe_hours' => 168,
                'timeframe_desc' => 'Entro 7 giorni',
            ],
            [
                'function_id' => $funcRete?->id,
                'remediation_type' => 'Monitoraggio Rete',
                'name' => 'Regolarizzazione formazione obbligatoria',
                'description' => "Sollecito e iscrizione d'ufficio dei collaboratori ai corsi...",
                'timeframe_hours' => 720,
                'timeframe_desc' => 'Entro 30 giorni',
            ],
            [
                'function_id' => $funcGov?->id,  // Assegnato al CdA
                'remediation_type' => 'Assetto Organizzativo',
                'name' => 'Aggiornamento Manuale Operativo',
                'description' => 'Revisione del manuale e del sistema di deleghe...',
                'timeframe_hours' => 1440,
                'timeframe_desc' => 'Entro 60 giorni',
            ],
        ];

        foreach ($azioniDiRimedio as $item) {
            $remediation = Remediation::create([
                'audit_item_id' => $auditItem->id,  // Use actual audit item ID
                'business_function_id' => $item['function_id'],  // Inserisco il riferimento
                'remediation_type' => $item['remediation_type'],
                'name' => $item['name'],
                'description' => $item['description'],
                'timeframe_hours' => $item['timeframe_hours'],
                'timeframe_desc' => $item['timeframe_desc'],
            ]);

            // Aggiungi lo scope OAM a tutte le remediation
            //   $remediation->scopes()->syncWithoutDetaching([$oamScopeId]);
        }
    }
}
