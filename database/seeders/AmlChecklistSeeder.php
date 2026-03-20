<?php

namespace Database\Seeders;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmlChecklistSeeder extends Seeder
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
            ->where('code', 'AML')
            ->first();

        if (!$checklist) {
            $checklistId = DB::table('checklists')->insertGetId([
                'company_id' => $companyId,
                'name' => 'Adeguata Verifica Antiriciclaggio (KYC / AML)',
                'code' => 'AML',
                'type' => 'loan_management',  // Legata all'apertura di una pratica o anagrafica
                'description' => "Questionario per l'assolvimento degli obblighi di adeguata verifica della clientela ai sensi del D.Lgs. 231/2007.",
                'principal_id' => null,
                'is_practice' => 1,
                'is_audit' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // 2. Creazione delle Domande / Fasi AML
            $items = [
                // --- FASE 1: IDENTIFICAZIONE BASE E SCOPO ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '10',
                    'name' => 'Documento Identità Valido',
                    'item_code' => 'aml_doc_identita',
                    'question' => 'Caricare copia del Documento di Identità e Codice Fiscale (o Tessera Sanitaria) in corso di validità del cliente/esecutore.',
                    'description' => "L'identificazione deve avvenire preferibilmente in presenza.",
                    'is_required' => 1,
                    'n_documents' => 99,
                    'attach_model' => 'principal',
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '20',
                    'name' => 'Scopo e Natura del Rapporto',
                    'item_code' => 'aml_scopo_rapporto',
                    'question' => 'Descrivere sinteticamente lo scopo e la natura del rapporto (es. Richiesta mutuo per acquisto prima casa, Cessione del quinto per liquidità).',
                    'description' => 'Testo libero, obbligatorio ai fini AML.',
                    'is_required' => 1,
                    'n_documents' => 0,  // Sarà renderizzato come campo Testo
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // --- FASE 2: PERSONA FISICA VS GIURIDICA ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '30',
                    'name' => 'Tipologia Cliente',
                    'item_code' => 'aml_is_azienda',
                    'question' => 'Il cliente è una Persona Giuridica (es. SRL, SPA, SNC) o un Ente?',
                    'description' => 'Rispondere 1 (Vero) se Società/Ente, 0 (Falso) se Persona Fisica.',
                    'is_required' => 1,
                    'n_documents' => 0,  // Toggle Vero/Falso
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '40',
                    'name' => 'Visura Camerale',
                    'item_code' => 'aml_visura',
                    'question' => 'Caricare la Visura Camerale aggiornata (non antecedente a 6 mesi).',
                    'description' => "Necessaria per verificare i poteri di firma dell'esecutore e l'assetto societario.",
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'principal',
                    'dependency_type' => 'show_if',
                    'depends_on_code' => 'aml_is_azienda',
                    'depends_on_value' => '1',
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '50',
                    'name' => 'Dichiarazione Titolare Effettivo',
                    'item_code' => 'aml_titolare_effettivo',
                    'question' => 'Caricare il modulo di Dichiarazione del Titolare Effettivo firmato dal legale rappresentante.',
                    'description' => 'Obbligatorio individuare la persona fisica che detiene più del 25% del capitale o esercita il controllo.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'principal',
                    'dependency_type' => 'show_if',
                    'depends_on_code' => 'aml_is_azienda',
                    'depends_on_value' => '1',
                ],
                // --- FASE 3: VERIFICA LISTE E PEP ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '60',
                    'name' => 'Status PEP',
                    'item_code' => 'aml_is_pep',
                    'question' => 'Il cliente o il titolare effettivo rientra nella definizione di Persona Politicamente Esposta (PEP)?',
                    'description' => 'Verificare tramite le liste o la dichiarazione del cliente.',
                    'is_required' => 1,
                    'n_documents' => 0,  // Toggle
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '70',
                    'name' => 'Autorizzazione Superiore PEP',
                    'item_code' => 'aml_auth_pep',
                    'question' => "Caricare l'autorizzazione del Direttore / Responsabile AML per l'apertura del rapporto con il cliente PEP.",
                    'description' => "L'apertura di rapporti con PEP richiede l'approvazione dell'alta direzione.",
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => null,  // Legato alla pratica
                    'dependency_type' => 'show_if',
                    'depends_on_code' => 'aml_is_pep',
                    'depends_on_value' => '1',
                ],
                // --- FASE 4: PROFILATURA DI RISCHIO E ADEGUATA VERIFICA RAFFORZATA ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '80',
                    'name' => 'Rischio Antiriciclaggio Alto',
                    'item_code' => 'aml_rischio_alto',
                    'question' => 'A seguito del calcolo degli indicatori di anomalia (es. contanti, settore a rischio, PEP, ecc.), il profilo di rischio risulta ALTO?',
                    'description' => 'Rispondere 1 (Vero) se rischio alto, 0 (Falso) altrimenti.',
                    'is_required' => 1,
                    'n_documents' => 0,  // Toggle
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '90',
                    'name' => 'Adeguata Verifica Rafforzata (Origine Fondi)',
                    'item_code' => 'aml_verifica_rafforzata',
                    'question' => "Essendo il rischio ALTO, allegare documentazione comprovante l'origine dei fondi/patrimonio (es. atti notarili, successioni, estratti conto).",
                    'description' => 'Obbligatorio acquisire informazioni aggiuntive per mitigare il rischio.',
                    'is_required' => 1,
                    'n_documents' => 99,
                    'attach_model' => 'principal',
                    'dependency_type' => 'show_if',
                    'depends_on_code' => 'aml_rischio_alto',
                    'depends_on_value' => '1',
                ],
                // --- FASE 5: FIRMA QUESTIONARIO E CHIUSURA ---
                [
                    'checklist_id' => $checklistId,
                    'ordine' => '100',
                    'name' => 'Questionario AML Firmato',
                    'item_code' => 'aml_questionario_firmato',
                    'question' => 'Caricare il modulo di Adeguata Verifica (Questionario Antiriciclaggio) stampato, datato e firmato in originale dal cliente.',
                    'description' => 'Documento riepilogativo finale.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'principal',
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
            ];

            // Mappatura automatica per inserire i timestamp e formattare i dati
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
