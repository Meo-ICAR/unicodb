<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerAccessChecklistSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $companyId = '9f5b0a17-f03d-401e-9bf3-625768ee58b2';

        $checklistId = DB::table('checklists')->insertGetId([
            'company_id' => $companyId,
            'name' => 'Gestione Richiesta di Accesso ai Dati (Art. 15 GDPR)',
            'code' => 'GDPR_ACCESS_REQ',
            'type' => 'audit',
            'description' => "Procedura per l'evasione delle richieste di accesso, rettifica o cancellazione dati presentate dai clienti.",
            'is_practice' => 0,
            'is_audit' => 1,
            'is_template' => 1,
            'status' => 'da_compilare',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $items = [
            [
                'ordine' => '10', 'phase' => 'Ricezione',
                'name' => 'Modulo di Richiesta', 'item_code' => 'gdpr_req_doc',
                'question' => "Allegare l'istanza di accesso presentata dal cliente (email, PEC o raccomandata).",
                'description' => 'Verificare la data di ricezione per il calcolo del termine di 30 giorni.',
                'is_required' => 1, 'attach_model' => 'audit', 'n_documents' => 1,
                'document_type_code' => 'richiesta-accesso-gdpr'
            ],
            [
                'ordine' => '20', 'phase' => 'Identificazione',
                'name' => 'Verifica Identità', 'item_code' => 'gdpr_verify_id',
                'question' => "È stata accertata l'identità del richiedente tramite documento valido?",
                'description' => 'Necessario per evitare data breach verso terzi non autorizzati.',
                'is_required' => 1, 'n_documents' => 0
            ],
            [
                'ordine' => '30', 'phase' => 'Istruttoria',
                'name' => 'Estrazione Dati', 'item_code' => 'gdpr_data_extraction',
                'question' => 'Sono stati estratti tutti i dati personali conservati (AUI, Pratiche, CRM, Contratti)?',
                'description' => "L'accesso deve coprire tutte le basi dati aziendali.",
                'is_required' => 1, 'n_documents' => 0
            ],
            [
                'ordine' => '40', 'phase' => 'Riscontro',
                'name' => 'Invio Risposta al Cliente', 'item_code' => 'gdpr_response_sent',
                'question' => 'Allegare copia della risposta inviata al cliente con i dati richiesti.',
                'description' => 'La risposta deve includere le finalità del trattamento e il periodo di conservazione.',
                'is_required' => 1, 'attach_model' => 'audit', 'n_documents' => 1,
                'document_type_code' => 'riscontro-accesso-gdpr'
            ],
            [
                'ordine' => '50', 'phase' => 'Chiusura', 'is_phaseclose' => 1,
                'name' => 'Rispetto dei Termini', 'item_code' => 'gdpr_deadline_check',
                'question' => 'Il riscontro è stato fornito entro 30 giorni dalla richiesta?',
                'description' => 'In caso di ritardo, motivare le ragioni della proroga (max 90 giorni totali).',
                'is_required' => 1, 'n_documents' => 0
            ],
        ];

        foreach ($items as $item) {
            $item['checklist_id'] = $checklistId;
            $item['created_at'] = $now;
            $item['updated_at'] = $now;
            DB::table('checklist_items')->insert($item);
        }
    }
}
