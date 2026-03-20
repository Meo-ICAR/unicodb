<?php
namespace Database\Seeders;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CessioneQuintoChecklistSeeder extends Seeder
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
            ->where('code', 'CQS')
            ->first();

        if (!$checklist) {
            $checklistId = DB::table('checklists')->insertGetId([
                'company_id' => $companyId,
                'name' => 'Lavorazione Pratica - Cessione del Quinto (CQS/CQP)',
                'code' => 'CQS',
                'type' => 'loan_management',
                'description' => 'Iter completo per la lavorazione di una pratica di Cessione del Quinto dello Stipendio o della Pensione, dalla raccolta documenti al benestare.',
                'principal_id' => null,
                'is_practice' => 1,  // È legata a una pratica operativa
                'is_audit' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // 2. Creazione delle Domande / Fasi di lavorazione
            $items = [
                // --- FASE 1: ANAGRAFICA E IDENTIFICAZIONE ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '10',
                    'name' => 'Documenti di Identità',
                    'item_code' => 'cqs_doc_identita',
                    'question' => 'Caricare copia fronte/retro del Documento di Identità e Tessera Sanitaria in corso di validità.',
                    'description' => 'Assicurarsi che i documenti siano ben leggibili e non scadano entro i prossimi 30 giorni.',
                    'is_required' => 1,
                    'n_documents' => 99,  // Multipli (CI + CF)
                    'attach_model' => 'principal',  // Si aggancia al cliente (principal)
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '20',
                    'name' => 'Privacy e Antiriciclaggio',
                    'item_code' => 'cqs_aml_privacy',
                    'question' => 'Modulistica Privacy e Questionario di Adeguata Verifica (KYC) firmati dal cliente.',
                    'description' => 'Verificare che le firme siano conformi ai documenti di identità.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'principal',
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // --- FASE 2: TIPOLOGIA CLIENTE E REDDITO ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '30',
                    'name' => 'Tipologia Richiedente',
                    'item_code' => 'cqs_is_pensionato',
                    'question' => 'Il cliente richiedente è un Pensionato (INPS / Ex-INPDAP)?',
                    'description' => 'Rispondere 1 (Vero) per Pensionati, 0 (Falso) per Dipendenti (Pubblici/Privati).',
                    'is_required' => 1,
                    'n_documents' => 0,  // Solo risposta (0 o 1)
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // --- FASE 2A: DOCUMENTI PENSIONATO ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '40',
                    'name' => 'Cedolini Pensione',
                    'item_code' => 'cqs_cedolini',
                    'question' => 'Caricare gli ultimi 2 cedolini della pensione.',
                    'description' => null,
                    'is_required' => 1,
                    'n_documents' => 99,
                    'attach_model' => 'principal',
                    'dependency_type' => 'show_if',
                    'depends_on_code' => 'cqs_is_pensionato',
                    'depends_on_value' => '1',  // Mostra se pensionato
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '50',
                    'name' => 'Quota Cedibile',
                    'item_code' => 'cqs_quota_cedibile',
                    'question' => "Caricare il certificato di Quota Cedibile rilasciato dall'Ente Pensionistico.",
                    'description' => 'Verificare che la data di rilascio non sia antecedente a 60 giorni.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'principal',
                    'dependency_type' => 'show_if',
                    'depends_on_code' => 'cqs_is_pensionato',
                    'depends_on_value' => '1',
                ],
                // --- FASE 2B: DOCUMENTI DIPENDENTE ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '60',
                    'name' => 'Buste Paga',
                    'item_code' => 'cqs_buste_paga',
                    'question' => "Caricare le ultime 2 buste paga e l'ultimo CUD/CU.",
                    'description' => null,
                    'is_required' => 1,
                    'n_documents' => 99,
                    'attach_model' => 'principal',
                    'dependency_type' => 'hide_if',
                    'depends_on_code' => 'cqs_is_pensionato',
                    'depends_on_value' => '1',  // Nascondi se pensionato (quindi mostra ai dipendenti)
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '70',
                    'name' => 'Certificato di Stipendio',
                    'item_code' => 'cqs_cert_stipendio',
                    'question' => "Caricare il Certificato di Stipendio debitamente timbrato e firmato dall'Azienda / Ente.",
                    'description' => 'Controllare accuratamente la presenza di altre trattenute in busta (pignoramenti, alimenti, etc.).',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'principal',
                    'dependency_type' => 'hide_if',
                    'depends_on_code' => 'cqs_is_pensionato',
                    'depends_on_value' => '1',
                ],
                // --- FASE 3: CONTRATTUALISTICA E ASSICURAZIONE ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '80',
                    'name' => 'Trasparenza SECCI / IEBCC',
                    'item_code' => 'cqs_secci',
                    'question' => 'Il modulo SECCI / IEBCC è stato consegnato, illustrato e firmato dal cliente?',
                    'description' => 'Allega la copia firmata.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => null,  // Si aggancia alla checklist/pratica
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '90',
                    'name' => 'Emissione Polizze',
                    'item_code' => 'cqs_polizze',
                    'question' => 'Caricare i certificati delle polizze obbligatorie emesse (Rischio Vita e/o Rischio Impiego).',
                    'description' => "Assicurarsi che la compagnia abbia dato parere favorevole all'assunzione del rischio.",
                    'is_required' => 1,
                    'n_documents' => 99,
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '100',
                    'name' => 'Contratto Firmato',
                    'item_code' => 'cqs_contratto',
                    'question' => 'Caricare il contratto di Cessione del Quinto completo di tutte le firme del cliente.',
                    'description' => 'Verificare la firma di tutti gli allegati contrattuali.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // --- FASE 4: NOTIFICA ED EROGAZIONE ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '110',
                    'name' => 'Notifica Contratto',
                    'item_code' => 'cqs_notifica',
                    'question' => "Inserisci il giorno di notifica del contratto all'Ente/Azienda Terza (PEC o Ufficiale Giudiziario) ed allega ricevuta.",
                    'description' => "Il testo conterrà la data e l'allegato la ricevuta di notifica.",
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
                    'name' => 'Benestare Ente / Azienda',
                    'item_code' => 'cqs_benestare',
                    'question' => "L'Azienda / Ente Pensionistico ha rilasciato l'Atto di Benestare definitivo?",
                    'description' => 'Documento indispensabile per procedere alla messa in quota e successiva liquidazione.',
                    'is_required' => 0,  // Spesso il benestare ritarda rispetto alla notifica
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
