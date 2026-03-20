<?php
namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgentOnboardingChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $companyId = '9f5b0a17-f03d-401e-9bf3-625768ee58b2';  // ID dalla tua struttura precedente

        // 1. Inserimento Testata Checklist
        $checklistId = DB::table('checklists')->insertGetId([
            'company_id' => $companyId,
            'name' => 'Onboarding e Contrattualizzazione Collaboratore',
            'code' => 'ONBOARDING_AGENT',
            'type' => 'audit',
            'description' => "Procedura di raccolta documentale e verifica requisiti (OAM/IVASS/Onorabilità) per l'attivazione di un nuovo collaboratore.",
            'is_practice' => 0,
            'is_audit' => 1,
            'is_template' => 1,
            'status' => 'da_compilare',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Definizione Item della Checklist
        $items = [
            [
                'ordine' => '10', 'phase' => 'Anagrafica', 'is_phaseclose' => 0,
                'name' => 'Documenti Identità e CF', 'item_code' => 'onb_identita',
                'question' => 'Caricare copia fronte/retro del Documento di Identità e del Codice Fiscale.',
                'description' => 'Assicurarsi che siano leggibili e non scaduti.',
                'descriptioncheck' => 'Verifica corrispondenza dati anagrafici.',
                'is_required' => 1, 'attach_model' => 'agent', 'n_documents' => 99,
                'document_type_code' => 'identita-codice-fiscale'
            ],
            [
                'ordine' => '20', 'phase' => 'Requisiti Onorabilità', 'is_phaseclose' => 0,
                'name' => 'Certificato Casellario Giudiziale', 'item_code' => 'onb_casellario',
                'question' => 'Caricare il Certificato del Casellario Giudiziale (non antecedente a 3 mesi).',
                'description' => 'Obbligatorio per verifica requisiti onorabilità OAM.',
                'descriptioncheck' => 'Assenza di condanne ostative.',
                'is_required' => 1, 'attach_model' => 'agent', 'n_documents' => 1,
                'document_type_code' => 'casellario-giudiziale'
            ],
            [
                'ordine' => '30', 'phase' => 'Requisiti Onorabilità', 'is_phaseclose' => 0,
                'name' => 'Carichi Pendenti', 'item_code' => 'onb_carichi_pendenti',
                'question' => 'Caricare il Certificato dei Carichi Pendenti (non antecedente a 3 mesi).',
                'description' => null,
                'descriptioncheck' => 'Verifica pendenze in corso.',
                'is_required' => 1, 'attach_model' => 'agent', 'n_documents' => 1,
                'document_type_code' => 'carichi-pendenti'
            ],
            [
                'ordine' => '40', 'phase' => 'Professionalità', 'is_phaseclose' => 0,
                'name' => 'Attestato OAM / IVASS', 'item_code' => 'onb_attestato_oam',
                'question' => "Caricare l'attestato di superamento prova valutativa OAM o formazione biennale aggiornata.",
                'description' => "Necessario per l'iscrizione/mantenimento nell'elenco dei collaboratori.",
                'descriptioncheck' => 'Verifica validità ore e data conseguimento.',
                'is_required' => 1, 'attach_model' => 'agent', 'n_documents' => 1,
                'document_type_code' => 'attestato-professionale'
            ],
            [
                'ordine' => '50', 'phase' => 'Professionalità', 'is_phaseclose' => 0,
                'name' => 'Polizza RC Professionale', 'item_code' => 'onb_polizza_rc',
                'question' => 'Caricare la quietanza o il certificato della Polizza RC Professionale individuale (se prevista).',
                'description' => 'Verificare se coperta dalla polizza cumulativa del Mediatore.',
                'descriptioncheck' => 'Controllo massimali e scadenza.',
                'is_required' => 1, 'attach_model' => 'agent', 'n_documents' => 1,
                'document_type_code' => 'polizza-rc'
            ],
            [
                'ordine' => '60', 'phase' => 'Società', 'is_phaseclose' => 0,
                'name' => 'Visura Camerale', 'item_code' => 'onb_visura',
                'question' => 'Il collaboratore opera come ditta individuale o società? Caricare la Visura.',
                'description' => 'Non necessaria per collaboratori persone fisiche senza P.IVA.',
                'descriptioncheck' => 'Verifica poteri di firma e oggetto sociale.',
                'is_required' => 0, 'attach_model' => 'agent', 'n_documents' => 1,
                'document_type_code' => 'visura-camerale'
            ],
            [
                'ordine' => '70', 'phase' => 'Contrattualizzazione', 'is_phaseclose' => 0,
                'name' => 'Contratto Firmato', 'item_code' => 'onb_contratto',
                'question' => "Caricare il contratto di collaborazione firmato digitalmente o scansione dell'originale.",
                'description' => null,
                'descriptioncheck' => 'Verifica presenza di tutte le sigle e firme.',
                'is_required' => 1, 'attach_model' => 'agent', 'n_documents' => 1,
                'document_type_code' => 'contratto-firmato'
            ],
            [
                'ordine' => '80', 'phase' => 'Amministrazione', 'is_phaseclose' => 0,
                'name' => 'Coordinate IBAN', 'item_code' => 'onb_iban',
                'question' => 'Caricare documento di sintesi del conto o modulo comunicazione IBAN.',
                'description' => "Necessario per l'accredito delle provvigioni.",
                'descriptioncheck' => 'Verifica intestazione conto (deve coincidere con il collaboratore).',
                'is_required' => 1, 'attach_model' => 'agent', 'n_documents' => 1,
                'document_type_code' => 'iban-coordinate'
            ],
            [
                'ordine' => '90', 'phase' => 'Compliance', 'is_phaseclose' => 1,
                'name' => 'Dichiarazione Antimafia', 'item_code' => 'onb_antimafia',
                'question' => "Caricare l'autocertificazione antimafia firmata.",
                'description' => 'Modulo standard interno.',
                'descriptioncheck' => 'Verifica firma.',
                'is_required' => 1, 'attach_model' => 'agent', 'n_documents' => 1,
                'document_type_code' => 'antimafia'
            ],
        ];

        // 3. Inserimento Items
        foreach ($items as $item) {
            $item['checklist_id'] = $checklistId;
            $item['created_at'] = $now;
            $item['updated_at'] = $now;
            DB::table('checklist_items')->insert($item);
        }
    }
}
