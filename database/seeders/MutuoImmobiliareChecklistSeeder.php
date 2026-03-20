<?php

namespace Database\Seeders;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MutuoImmobiliareChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $companyId = Company::first()->id;

        $checklist = DB::table('checklists')
            ->where('code', 'MUT')
            ->first();

        if (!$checklist) {
            // 1. Creazione della Checklist (Modello Mutuo)
            $checklistId = DB::table('checklists')->insertGetId([
                'company_id' => $companyId,
                'name' => 'Lavorazione Pratica - Mutuo Immobiliare',
                'code' => 'MUT',
                'type' => 'loan_management',
                'description' => "Iter di raccolta documentale e verifica per l'istruttoria di un Mutuo Ipotecario (Acquisto, Surroga o Ristrutturazione).",
                'principal_id' => null,
                'is_practice' => 1,
                'is_audit' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // 2. Creazione delle Domande / Fasi di lavorazione
            $items = [
                // --- FASE 1: TRASPARENZA E IDENTIFICAZIONE ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '10',
                    'name' => 'Privacy e Antiriciclaggio',
                    'item_code' => 'mutuo_aml_privacy',
                    'question' => 'Caricare Modulistica Privacy e Questionario di Adeguata Verifica (KYC) firmati dai richiedenti.',
                    'description' => 'Includere i documenti di identità in corso di validità se non già presenti a sistema.',
                    'is_required' => 1,
                    'n_documents' => 99,
                    'attach_model' => 'principal',
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // --- FASE 2: DOCUMENTAZIONE REDDITUALE ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '20',
                    'name' => 'Tipologia Reddituale',
                    'item_code' => 'mutuo_is_autonomo',
                    'question' => 'Il richiedente principale è un Lavoratore Autonomo / Libero Professionista (P.IVA)?',
                    'description' => 'Rispondere 1 (Vero) per Autonomi/P.IVA, 0 (Falso) per Lavoratori Dipendenti o Pensionati.',
                    'is_required' => 1,
                    'n_documents' => 0,  // Toggle Vero/Falso
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // 2A: Dipendente
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '30',
                    'name' => 'Reddito Dipendente / Pensionato',
                    'item_code' => 'mutuo_doc_dipendente',
                    'question' => "Caricare le ultime 3 buste paga (o cedolini pensione) e l'ultimo CUD/CU.",
                    'description' => 'Per i dipendenti neo-assunti, allegare anche copia del contratto di lavoro.',
                    'is_required' => 1,
                    'n_documents' => 99,
                    'attach_model' => 'principal',
                    'dependency_type' => 'hide_if',
                    'depends_on_code' => 'mutuo_is_autonomo',
                    'depends_on_value' => '1',  // Nascosto se è autonomo (mostrato ai dipendenti)
                ],
                // 2B: Autonomo
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '40',
                    'name' => 'Reddito Autonomo',
                    'item_code' => 'mutuo_doc_autonomo',
                    'question' => 'Caricare gli ultimi 2 Modelli Unici (completi di ricevuta di presentazione telematica) e la Visura Camerale / Certificato di attribuzione P.IVA.',
                    'description' => 'Verificare la regolarità dei versamenti F24 se richiesti dalla banca.',
                    'is_required' => 1,
                    'n_documents' => 99,
                    'attach_model' => 'principal',
                    'dependency_type' => 'show_if',
                    'depends_on_code' => 'mutuo_is_autonomo',
                    'depends_on_value' => '1',  // Mostrato se è autonomo
                ],
                // Estratti Conto (Per tutti)
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '50',
                    'name' => 'Estratti Conto Corrente',
                    'item_code' => 'mutuo_estratti_conto',
                    'question' => "Caricare l'estratto conto integrale degli ultimi 3/6 mesi del conto principale.",
                    'description' => "Il conto deve mostrare l'accredito dello stipendio/entrate e il pagamento di eventuali altri impegni finanziari.",
                    'is_required' => 1,
                    'n_documents' => 99,
                    'attach_model' => 'principal',
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // --- FASE 3: DOCUMENTAZIONE IMMOBILIARE ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '60',
                    'name' => 'Finalità Acquisto',
                    'item_code' => 'mutuo_is_acquisto',
                    'question' => "Il mutuo è finalizzato all'acquisto di un immobile?",
                    'description' => 'Rispondere 1 (Vero) se è un Acquisto, 0 (Falso) se è Surroga, Liquidità o Ristrutturazione su immobile già di proprietà.',
                    'is_required' => 1,
                    'n_documents' => 0,  // Toggle Vero/Falso
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '70',
                    'name' => "Compromesso / Proposta d'Acquisto",
                    'item_code' => 'mutuo_compromesso',
                    'question' => "Caricare il Preliminare di Compravendita (Compromesso) registrato o la Proposta d'Acquisto accettata.",
                    'description' => 'Includere copia degli assegni versati a titolo di caparra.',
                    'is_required' => 1,
                    'n_documents' => 99,
                    'attach_model' => null,  // Legato alla pratica
                    'dependency_type' => 'show_if',
                    'depends_on_code' => 'mutuo_is_acquisto',
                    'depends_on_value' => '1',
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '80',
                    'name' => 'Atto di Provenienza',
                    'item_code' => 'mutuo_atto_provenienza',
                    'question' => "Caricare l'Atto di Provenienza (Rogito precedente, Dichiarazione di Successione o Donazione) del venditore / attuale proprietario.",
                    'description' => 'Documento fondamentale per la Relazione Notarile.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '90',
                    'name' => 'Planimetria e Visura Catastale',
                    'item_code' => 'mutuo_catasto',
                    'question' => "Caricare la Planimetria Catastale aggiornata e la Visura Storica dell'immobile (e relative pertinenze).",
                    'description' => 'Verificare la conformità tra lo stato di fatto e la planimetria.',
                    'is_required' => 1,
                    'n_documents' => 99,
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // --- FASE 4: ISTRUTTORIA BANCARIA E STIPULA ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '100',
                    'name' => 'PIES (Prospetto Informativo)',
                    'item_code' => 'mutuo_pies',
                    'question' => 'Il PIES (Prospetto Informativo Europeo Standardizzato) è stato generato, consegnato e firmato dal cliente?',
                    'description' => 'Allega il PIES firmato.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '110',
                    'name' => 'Relazione Notarile Preliminare (RNP)',
                    'item_code' => 'mutuo_rnp',
                    'question' => 'Caricare la Relazione Notarile Preliminare (ventennale) redatta dal Notaio incaricato.',
                    'description' => "Atesta l'assenza di ipoteche o gravami pregiudizievoli.",
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '120',
                    'name' => 'Perizia Tecnica',
                    'item_code' => 'mutuo_perizia',
                    'question' => "Il perito incaricato dalla banca ha depositato l'elaborato peritale? Allegane copia se disponibile o inserisci il valore cauzionale nelle note.",
                    'description' => "Verificare che il valore di perizia copra regolarmente l'LTV (Loan To Value) richiesto.",
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '130',
                    'name' => 'Polizza Incendio e Scoppio',
                    'item_code' => 'mutuo_polizza_incendio',
                    'question' => 'Caricare il certificato o la quietanza della Polizza Incendio e Scoppio obbligatoria.',
                    'description' => "Se la polizza è esterna alla banca, assicurarsi che preveda il vincolo a favore dell'istituto erogante.",
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
            ];

            // Mappatura automatica dei campi default
            $formattedItems = array_map(function ($item) use ($now) {
                return array_merge([
                    'answer' => null,
                    'annotation' => null,
                    'attach_model_id' => null,
                    'repeatable_code' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], $item);
            }, $items);

            DB::table('checklist_items')->insert($formattedItems);
        }
    }
}
