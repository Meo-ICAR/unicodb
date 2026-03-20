<?php

namespace Database\Seeders;

use App\Models\PracticeScope;
use App\Models\ProcessTask;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProcessTaskSeeder extends Seeder
{
    public function run(): void
    {
        // Definizione dei flussi per tipologia di prodotto
        $workflowTemplates = [
            // CESSIONE DEL QUINTO (CQS/CQP)
            'CessioneCQS' => [
                ['name' => 'Raccolta Documenti e KYC', 'order' => 10],
                ['name' => 'Richiesta Certificato di Stipendio / Allegato A', 'order' => 20],
                ['name' => 'Verifica Merito e Fattibilità Assicurativa', 'order' => 30],
                ['name' => 'Caricamento Portale Banca / Finanziaria', 'order' => 40],
                ['name' => 'Emissione e Firma Contratti', 'order' => 50],
                ['name' => 'Notifica Atto al Terzo Ceduto', 'order' => 60],
                ['name' => 'Ottenimento Atto di Benestare', 'order' => 70],
                ['name' => 'Liquidazione e Post-Vendita', 'order' => 80],
            ],
            // MUTUI IPOTECARI
            'MUT_IPOTECARIO' => [
                ['name' => 'Analisi Preliminare e Consulenza', 'order' => 10],
                ['name' => 'Raccolta Documenti Reddituali e Immobile', 'order' => 20],
                ['name' => 'Istruttoria e Delibera Reddituale', 'order' => 30],
                ['name' => 'Prenotazione Perizia Immobile', 'order' => 40],
                ['name' => 'Relazione Notarile Preliminare (RNP)', 'order' => 50],
                ['name' => 'Delibera Definitiva e Stipula', 'order' => 60],
            ],
            // PRESTITI PERSONALI
            'CRED_PERS' => [
                ['name' => 'Intervista Cliente e Screening Creditizio', 'order' => 10],
                ['name' => 'Acquisizione Documentale e Privacy', 'order' => 20],
                ['name' => 'Invio Pratica e Esito Automatico', 'order' => 30],
                ['name' => 'Erogazione su Conto Corrente', 'order' => 40],
            ],
            // AZIENDALE / CHIROGRAFARIO
            'Aziendale' => [
                ['name' => 'Analisi Centrale Rischi e Bilanci', 'order' => 10],
                ['name' => 'Redazione Business Plan / Report Istruttorio', 'order' => 20],
                ['name' => 'Richiesta Garanzia MCC (Medio Credito Centrale)', 'order' => 30],
                ['name' => 'Delibera e Perfezionamento', 'order' => 40],
            ]
        ];

        foreach ($workflowTemplates as $scopeCode => $tasks) {
            $scope = PracticeScope::where('code', $scopeCode)->first();

            if ($scope) {
                foreach ($tasks as $taskData) {
                    ProcessTask::updateOrCreate(
                        [
                            'practice_scope_id' => $scope->id,
                            'slug' => Str::slug($scopeCode . '-' . $taskData['name']),
                        ],
                        [
                            'name' => $taskData['name'],
                            'sort_order' => $taskData['order'],
                        ]
                    );
                }
            }
        }
    }
}
