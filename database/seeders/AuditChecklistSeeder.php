<?php

namespace Database\Seeders;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuditChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $companyId = Company::first()->id;
        // 1. Creazione della Checklist (Template) - solo se non esiste
        $checklist = DB::table('checklists')
            ->where('code', 'AUDIT_RETE_AGENTI')
            ->first();

        if (!$checklist) {
            $checklistId = DB::table('checklists')->insertGetId([
                'company_id' => $companyId,  // Lasciato null per essere disponibile a tutti i tenant/globale
                'name' => 'Verifica Ispettiva Ordinaria - Rete Agenti',
                'code' => 'AUDIT_RETE_AGENTI',
                'type' => 'audit',
                'description' => "Checklist standard per l'audit periodico (OAM/Antiriciclaggio) dei collaboratori e delle agenzie della rete commerciale.",
                'principal_id' => null,
                'is_practice' => 0,
                'is_audit' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $checklistId = $checklist->id;
        }

        // 2. Creazione delle Domande / Elementi della Checklist
        // Utilizziamo multipli di 10 per l'ordine, così è facile inserire nuove domande in mezzo in futuro.
        $items = [
            // --- AREA 1: TRASPARENZA E LOCALI ---
            [
                'checklist_id' => $checklistId,
                'ordine' => '10',
                'name' => 'Esposizione Targa',
                'item_code' => 'targa_oam',
                'question' => "È regolarmente esposta la targa esterna/interna con l'indicazione dell'iscrizione OAM del Mediatore e dell'Agente?",
                'description' => "Verificare sia l'ingresso che i locali interni.",
                'is_required' => 1,
                'n_documents' => 0,
                'attach_model' => null,
                'attach_model_id' => null,
                'repeatable_code' => null,
                'dependency_type' => null,
                'depends_on_code' => null,
                'depends_on_value' => null,
                'answer' => null,
                'annotation' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'checklist_id' => $checklistId,
                'ordine' => '20',
                'name' => 'Bacheca Trasparenza',
                'item_code' => 'bacheca_trasparenza',
                'question' => 'Sono esposti e liberamente accessibili al pubblico il documento "Principali Diritti del Cliente" e i Fogli Informativi aggiornati?',
                'description' => 'Rispondere 1 (Vero) se presenti e aggiornati, 0 (Falso) se assenti o non aggiornati.',
                'is_required' => 1,
                'n_documents' => 0,
                'dependency_type' => null,
                'depends_on_code' => null,
                'depends_on_value' => null,
            ],
            [
                'checklist_id' => $checklistId,
                'ordine' => '30',
                'name' => 'Foto Bacheca',
                'item_code' => 'foto_bacheca',
                'question' => 'Scatta e allega una foto della bacheca trasparenza.',
                'description' => 'La foto deve rendere leggibili almeno i titoli dei documenti esposti.',
                'is_required' => 1,
                'n_documents' => 1,  // Richiede 1 allegato
                'attach_model' => 'audit',  // L'allegato finisce sull'entità Audit
                'dependency_type' => 'show_if',
                'depends_on_code' => 'bacheca_trasparenza',
                'depends_on_value' => '1',  // Mostrato solo se la bacheca c'è
            ],
            [
                'checklist_id' => $checklistId,
                'ordine' => '40',
                'name' => 'Motivo Assenza Trasparenza',
                'item_code' => 'motivo_no_trasparenza',
                'question' => 'Dettagliare i motivi della mancata esposizione della documentazione di trasparenza e le azioni correttive richieste.',
                'description' => 'Campo testuale obbligatorio in caso di anomalia.',
                'is_required' => 1,
                'n_documents' => 0,
                'dependency_type' => 'hide_if',
                'depends_on_code' => 'bacheca_trasparenza',
                'depends_on_value' => '1',  // Nascosto se la bacheca c'è (quindi mostrato se non c'è)
            ],
            // --- AREA 2: REQUISITI PROFESSIONALI ---
            [
                'checklist_id' => $checklistId,
                'ordine' => '50',
                'name' => 'Aggiornamento OAM',
                'item_code' => 'aggiornamento_oam',
                'question' => 'Il collaboratore ha completato le 60 ore di aggiornamento biennale obbligatorio OAM?',
                'description' => "Verificare gli attestati nell'area riservata o cartacei.",
                'is_required' => 1,
                'n_documents' => 0,
                'dependency_type' => null,
                'depends_on_code' => null,
                'depends_on_value' => null,
            ],
            [
                'checklist_id' => $checklistId,
                'ordine' => '60',
                'name' => 'Vendita Prodotti Assicurativi',
                'item_code' => 'vende_assicurazioni',
                'question' => "L'agente propone alla clientela anche prodotti assicurativi accessori (es. polizze CPI collegate ai finanziamenti)?",
                'description' => null,
                'is_required' => 1,
                'n_documents' => 0,
                'dependency_type' => null,
                'depends_on_code' => null,
                'depends_on_value' => null,
            ],
            [
                'checklist_id' => $checklistId,
                'ordine' => '70',
                'name' => 'Iscrizione IVASS',
                'item_code' => 'iscrizione_ivass',
                'question' => "L'agente è regolarmente iscritto al RUI (Registro IVASS) Sezione E?",
                'description' => 'Obbligatorio per chi colloca polizze.',
                'is_required' => 1,
                'n_documents' => 0,
                'dependency_type' => 'show_if',
                'depends_on_code' => 'vende_assicurazioni',
                'depends_on_value' => '1',
            ],
            [
                'checklist_id' => $checklistId,
                'ordine' => '80',
                'name' => 'Allegati Formazione',
                'item_code' => 'doc_ivass_oam',
                'question' => "Allega gli attestati formativi OAM / IVASS dell'ultimo biennio.",
                'description' => 'Caricare tutti i PDF degli attestati.',
                'is_required' => 0,
                'n_documents' => 99,  // Allegati Multipli consentiti
                'attach_model' => 'agent',  // Gli attestati vengono salvati nel profilo dell'Agente
                'dependency_type' => null,
                'depends_on_code' => null,
                'depends_on_value' => null,
            ],
            // --- AREA 3: ANTIRICICLAGGIO (AML) ---
            [
                'checklist_id' => $checklistId,
                'ordine' => '90',
                'name' => 'Gestione Clienti PEP',
                'item_code' => 'gestione_pep',
                'question' => 'Nel periodo in esame sono stati censiti clienti qualificabili come PEP (Persone Politicamente Esposte)?',
                'description' => "Verificare l'elenco pratiche.",
                'is_required' => 1,
                'n_documents' => 0,
                'dependency_type' => null,
                'depends_on_code' => null,
                'depends_on_value' => null,
            ],
            [
                'checklist_id' => $checklistId,
                'ordine' => '100',
                'name' => 'Autorizzazione PEP',
                'item_code' => 'autorizzazione_pep',
                'question' => "È stata richiesta e ottenuta l'autorizzazione dalla Direzione/AML Manager prima di procedere con le pratiche dei clienti PEP?",
                'description' => 'Come da policy antiriciclaggio interna.',
                'is_required' => 1,
                'n_documents' => 0,
                'dependency_type' => 'show_if',
                'depends_on_code' => 'gestione_pep',
                'depends_on_value' => '1',
            ],
        ];

        // Aggiungo i timestamp e i campi null di default per ogni record
        $formattedItems = array_map(function ($item) use ($now) {
            return array_merge([
                'answer' => null,
                'annotation' => null,
                'attach_model' => null,
                'attach_model_id' => null,
                'repeatable_code' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ], $item);
        }, $items);

        DB::table('checklist_items')->insert($formattedItems);
    }
}
