<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SOSChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $checklistId = DB::table('checklists')->insertGetId([
            'name' => 'Segnalazione Operazioni Sospette (SOS)',
            'code' => 'SOS_WORKFLOW',
            'type' => 'audit',
            'description' => "Analisi degli indicatori di anomalia per la determinazione dell'obbligo di segnalazione alla UIF.",
            'is_practice' => 1,
            'is_audit' => 1,
            'is_template' => 1,
            'status' => 'da_compilare',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $items = [
            [
                'ordine' => '10', 'phase' => 'Analisi Soggettiva',
                'name' => 'Profilo Cliente Coerente', 'item_code' => 'sos_coerenza_profilo',
                'question' => "L'operazione è coerente con il profilo economico-patrimoniale e l'attività dichiarata dal cliente?",
                'description' => "Verificare l'ultima dichiarazione dei redditi o bilancio disponibile.",
                'is_required' => 1, 'n_documents' => 0
            ],
            [
                'ordine' => '20', 'phase' => 'Indicatori di Anomalia',
                'name' => 'Utilizzo di Contante', 'item_code' => 'sos_cash_usage',
                'question' => 'Sono presenti frazionamenti di operazioni in contanti o prelievi ingiustificati sopra soglia?',
                'description' => 'Indicatori 1.1 e 1.2 delle istruzioni di vigilanza.',
                'is_required' => 1, 'n_documents' => 0
            ],
            [
                'ordine' => '30', 'phase' => 'Indicatori di Anomalia',
                'name' => 'Terzi Interposti', 'item_code' => 'sos_terzi_interposti',
                'question' => "L'operazione sembra essere disposta per conto di terzi o con l'interposizione di prestanome?",
                'description' => 'Verificare deleghe di firma o beneficiari effettivi occulti.',
                'is_required' => 1, 'n_documents' => 0
            ],
            [
                'ordine' => '40', 'phase' => 'Documentazione',
                'name' => 'Relazione Tecnica Istruttoria', 'item_code' => 'sos_relazione_interna',
                'question' => 'Caricare la relazione interna firmata dal Responsabile AML che giustifica la decisione.',
                'description' => "Documento obbligatorio per l'archiviazione o la segnalazione.",
                'is_required' => 1, 'attach_model' => 'audit', 'n_documents' => 1,
                'document_type_code' => 'relazione-sos'
            ],
            [
                'ordine' => '50', 'phase' => 'Decisione',
                'name' => 'Esito Istruttoria', 'item_code' => 'sos_esito_finale',
                'question' => 'Si procede con la segnalazione alla UIF?',
                'description' => "In caso di NO, motivare nell'answer l'infondatezza del sospetto.",
                'is_required' => 1, 'n_documents' => 0
            ],
            [
                'ordine' => '60', 'phase' => 'Chiusura', 'is_phaseclose' => 1,
                'name' => 'Ricevuta Portale UIF', 'item_code' => 'sos_ricevuta_uif',
                'question' => 'Caricare la ricevuta di invio generata dal portale INFOSTAT / UIF.',
                'description' => "Necessaria solo se l'esito della domanda precedente è SI.",
                'is_required' => 0, 'attach_model' => 'audit', 'n_documents' => 1,
                'document_type_code' => 'ricevuta-sos-uif',
                'depends_on_code' => 'sos_esito_finale', 'depends_on_value' => '1', 'dependency_type' => 'show_if'
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
