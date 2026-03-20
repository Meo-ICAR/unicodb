<?php

namespace Database\Seeders;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankAuditCompanyChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $companyId = Company::first()->id;

        $checklist = DB::table('checklists')
            ->where('code', 'BANK_AUDIT')
            ->first();

        if (!$checklist) {
            $checklistId = DB::table('checklists')->insertGetId([
                'company_id' => $companyId,
                'name' => 'Verifica Ispettiva Mandante - Audit Banca su Mediatore',
                'code' => 'BANK_AUDIT',
                'type' => 'audit',
                'description' => 'Checklist utilizzata dalle Banche (Principals) per auditare annualmente la società di Mediazione Creditizia (Company) su Governance, AML, Privacy e Rete.',
                'principal_id' => null,  // Potrebbe essere valorizzato se si crea un template specifico per una singola banca
                'is_practice' => 0,
                'is_audit' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // 2. Creazione delle Domande / Voci di Audit
            $items = [
                // --- FASE 1: GOVERNANCE E REQUISITI OAM ---
                [
                    'ordine' => '10',
                    'name' => 'Visura Camerale Aggiornata',
                    'item_code' => 'bank_visura_company',
                    'question' => 'Caricare la Visura Camerale Ordinaria della società aggiornata agli ultimi 3 mesi.',
                    'description' => 'Verifica della compagine sociale, dei poteri di firma e assenza di procedure concorsuali.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'company',  // Il documento appartiene al Mediatore (Company)
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'ordine' => '20',
                    'name' => 'Attestazione Iscrizione OAM',
                    'item_code' => 'bank_oam_company',
                    'question' => 'Caricare il certificato o la contabile di pagamento del contributo annuale OAM della Società.',
                    'description' => "A prova del mantenimento dei requisiti di iscrizione all'elenco dei Mediatori Creditizi.",
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'company',
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'ordine' => '30',
                    'name' => 'Polizza RC Professionale',
                    'item_code' => 'bank_rc_company',
                    'question' => 'Caricare copia della Polizza di Responsabilità Civile Professionale in corso di validità.',
                    'description' => 'Verificare che i massimali siano conformi alla normativa vigente in base ai volumi intermediati.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'company',
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // --- FASE 2: ANTIRICICLAGGIO (AML) E PROCEDURE INTERNE ---
                [
                    'ordine' => '40',
                    'name' => 'Manuale Antiriciclaggio',
                    'item_code' => 'bank_manuale_aml',
                    'question' => 'La società è dotata di un Manuale delle Procedure Antiriciclaggio (AML) aggiornato?',
                    'description' => 'Rispondere 1 (Vero) se presente e aggiornato, 0 (Falso) in caso di anomalie.',
                    'is_required' => 1,
                    'n_documents' => 0,  // Toggle Vero/Falso
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'ordine' => '50',
                    'name' => 'Copia Manuale AML',
                    'item_code' => 'bank_doc_manuale_aml',
                    'question' => 'Allegare copia del Manuale AML o del verbale del CdA di approvazione dello stesso.',
                    'description' => 'Il documento sarà esaminato dalla funzione Compliance della Banca.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'company',
                    'dependency_type' => 'show_if',
                    'depends_on_code' => 'bank_manuale_aml',
                    'depends_on_value' => '1',
                ],
                [
                    'ordine' => '60',
                    'name' => 'Relazione Annuale Funzione Controllo',
                    'item_code' => 'bank_relazione_controlli',
                    'question' => 'Caricare la Relazione Annuale della Funzione di Controllo Interno / Compliance (se prevista per dimensioni).',
                    'description' => 'Inserire nelle note se la società è esentata per limiti dimensionali.',
                    'is_required' => 1,
                    'n_documents' => 1,
                    'attach_model' => 'company',
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // --- FASE 3: PRIVACY, GDPR E IT SECURITY ---
                [
                    'ordine' => '70',
                    'name' => 'Nomina DPO',
                    'item_code' => 'bank_has_dpo',
                    'question' => 'La società ha nominato un Data Protection Officer (DPO)?',
                    'description' => 'Rispondere 1 (Vero) se nominato e comunicato al Garante.',
                    'is_required' => 1,
                    'n_documents' => 0,  // Toggle
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'ordine' => '80',
                    'name' => 'Documentazione GDPR',
                    'item_code' => 'bank_doc_gdpr',
                    'question' => 'Caricare il Registro delle Attività di Trattamento e la policy di gestione dei Data Breach.',
                    'description' => 'Essenziale per garantire alla banca la sicurezza dei dati della clientela trasmessi.',
                    'is_required' => 1,
                    'n_documents' => 99,
                    'attach_model' => 'company',
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // --- FASE 4: CONTROLLO RETE E TRASPARENZA ---
                [
                    'ordine' => '90',
                    'name' => 'Presenza Rete Agenti',
                    'item_code' => 'bank_has_rete',
                    'question' => 'La società si avvale di una rete di collaboratori / dipendenti a contatto con il pubblico?',
                    'description' => 'Rispondere 1 (Vero) se ci sono dipendenti/collaboratori, 0 (Falso) se opera solo il legale rappresentante.',
                    'is_required' => 1,
                    'n_documents' => 0,
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'ordine' => '100',
                    'name' => 'Piano Ispezioni Rete',
                    'item_code' => 'bank_piano_ispezioni',
                    'question' => 'Caricare evidenza del piano di ispezioni effettuate dal Mediatore sui propri collaboratori.',
                    'description' => 'La Banca deve accertarsi che il Mediatore controlli la propria rete.',
                    'is_required' => 1,
                    'n_documents' => 99,
                    'attach_model' => 'company',
                    'dependency_type' => 'show_if',
                    'depends_on_code' => 'bank_has_rete',
                    'depends_on_value' => '1',
                ],
                [
                    'ordine' => '110',
                    'name' => 'Materiale Pubblicitario',
                    'item_code' => 'bank_materiale_pubblicitario',
                    'question' => 'Il materiale pubblicitario utilizzato (sito web, social, volantini) menziona i prodotti della Banca nel rispetto delle linee guida di Trasparenza fornite?',
                    'description' => 'Caricare eventuali screenshot o PDF del materiale promozionale sottoposto a verifica.',
                    'is_required' => 1,
                    'n_documents' => 99,  // L'ispettore della banca carica le prove documentali (foto/screenshot)
                    'attach_model' => 'audit',  // Questo resta nell'audit, non va nell'anagrafica generica del mediatore
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                // --- FASE 5: VERIFICA PRATICHE DELLA BANCA (TEST A CAMPIONE) ---
                [
                    'ordine' => '120',
                    'name' => 'Test Sostanziali su Pratiche Banca',
                    'item_code' => 'bank_test_pratiche',
                    'question' => "Sono state riscontrate anomalie (es. firme difformi, documenti alterati) sul campione di pratiche caricate verso questa Banca nell'ultimo semestre?",
                    'description' => 'Rispondere 1 (Vero) se ci sono anomalie, 0 (Falso) se il campione è regolare.',
                    'is_required' => 1,
                    'n_documents' => 0,  // Toggle
                    'attach_model' => null,
                    'dependency_type' => null,
                    'depends_on_code' => null,
                    'depends_on_value' => null,
                ],
                [
                    'ordine' => '130',
                    'name' => 'Dettaglio Anomalie Pratiche',
                    'item_code' => 'bank_dettaglio_anomalie',
                    'question' => 'Specificare i numeri di pratica coinvolti e il tipo di anomalia riscontrata.',
                    'description' => 'Campo testuale obbligatorio in caso di esito negativo del test a campione.',
                    'is_required' => 1,
                    'n_documents' => 0,  // Campo testuale
                    'attach_model' => null,
                    'dependency_type' => 'show_if',
                    'depends_on_code' => 'bank_test_pratiche',
                ]
            ];

            // Mappatura automatica dei campi default
            $formattedItems = array_map(function ($item) use ($now, $checklistId) {
                return array_merge([
                    'checklist_id' => $checklistId,
                    'answer' => null,
                    'annotation' => null,
                    'attach_model_id' => null,
                    'repeatable_code' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                    // Campi obbligatori della tabella checklist_items
                    'ordine' => $item['ordine'] ?? null,
                    'name' => $item['name'] ?? null,
                    'item_code' => $item['item_code'] ?? null,
                    'question' => $item['question'] ?? null,
                    'description' => $item['description'] ?? null,
                    'is_required' => $item['is_required'] ?? 0,
                    'n_documents' => $item['n_documents'] ?? 0,
                    'attach_model' => $item['attach_model'] ?? null,
                    'depends_on_code' => $item['depends_on_code'] ?? null,
                    'depends_on_value' => $item['depends_on_value'] ?? null,
                    'dependency_type' => $item['dependency_type'] ?? null,
                    'url_step' => $item['url_step'] ?? null,
                    'url_callback' => $item['url_callback'] ?? null,
                ], $item);
            }, $items);
            DB::table('checklist_items')->insert($formattedItems);

            $checklistAgentId = DB::table('checklists')
                ->where('code', 'OAM_RETE_10GG')
                ->first();

            if (!$checklistAgentId) {
                $checklistAgentId = DB::table('checklists')->insertGetId([
                    'company_id' => $companyId,
                    'type' => 'audit',
                    'principal_id' => null,  // Potrebbe essere valorizzato se si crea un template specifico per una singola banca
                    'is_practice' => 0,
                    'is_audit' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'name' => 'Carico/Scarico Rete (Regola 10 Giorni OAM)',
                    'code' => 'OAM_RETE_10GG',
                    'type' => 'audit',
                    'description' => "Procedura obbligatoria per comunicare a OAM inizio o fine del mandato di un collaboratore entro 10 giorni dall'evento.",
                ]);

                // 2. Creazione delle Domande / Voci di Audit
                $itemsAgent = [
                    // STEP 1: Scelta del tipo di operazione
                    [
                        'ordine' => '10',
                        'name' => 'Tipo Movimentazione',
                        'item_code' => 'MOV_TIPO',
                        'question' => 'Seleziona il tipo di comunicazione:',
                        'description' => 'Inserisci "carico" per un nuovo agente o "scarico" per una cessazione.',
                        'is_required' => 1,
                        'n_documents' => 0,
                    ],
                    // STEP 2: Data evento (Fondamentale per calcolare i 10 giorni)
                    [
                        'ordine' => '20',
                        'name' => 'Data Riferimento Evento',
                        'item_code' => 'MOV_DATA',
                        'question' => 'Qual è la data di firma del mandato o la data di efficacia della cessazione?',
                        'description' => 'ATTENZIONE: Da questa data hai esattamente 10 giorni solari per completare la procedura sul portale OAM.',
                        'is_required' => 1,
                        'n_documents' => 0,
                    ],
                    // STEP 3A (Condizionale CARICO): Contratto Firmato
                    [
                        'ordine' => '30',
                        'name' => 'Contratto di Collaborazione',
                        'item_code' => 'DOC_CONTRATTO',
                        'question' => "Allega il contratto di agenzia o lettera d'incarico firmata da entrambe le parti.",
                        'is_required' => 1,
                        'depends_on_code' => 'MOV_TIPO',
                        'depends_on_value' => 'carico',
                        'dependency_type' => 'show_if',
                        'attach_model' => 'agent',  // Si salva nel fascicolo dell'agente!
                        'n_documents' => 1,
                    ],
                    // STEP 3B (Condizionale CARICO): Requisiti Formativi
                    [
                        'ordine' => '31',
                        'name' => 'Attestato Formazione OAM',
                        'item_code' => 'DOC_FORMAZIONE',
                        'question' => "Allega l'attestato del corso di formazione iniziale o aggiornamento (60 ore / 30 ore).",
                        'is_required' => 1,
                        'depends_on_code' => 'MOV_TIPO',
                        'depends_on_value' => 'carico',
                        'dependency_type' => 'show_if',
                        'attach_model' => 'agent',
                        'n_documents' => 1,
                    ],
                    // STEP 3C (Condizionale SCARICO): Lettera Cessazione
                    [
                        'ordine' => '32',
                        'name' => 'Lettera di Cessazione / Dimissioni',
                        'item_code' => 'DOC_CESSAZIONE',
                        'question' => 'Allega la lettera di dimissioni del collaboratore o la revoca del mandato da parte della società.',
                        'is_required' => 1,
                        'depends_on_code' => 'MOV_TIPO',
                        'depends_on_value' => 'scarico',
                        'dependency_type' => 'show_if',
                        'attach_model' => 'agent',
                        'n_documents' => 1,
                    ],
                    // STEP 4: Azione sul Portale Esterno
                    [
                        'ordine' => '40',
                        'name' => 'Comunicazione Portale OAM',
                        'item_code' => 'AZIONE_PORTALE',
                        'question' => "Hai effettuato l'inserimento della variazione nell'area privata del portale OAM?",
                        'description' => "Clicca sul link per accedere direttamente all'area riservata OAM.",
                        'is_required' => 1,
                        'url_step' => 'https://www.organismo-am.it/area-privata',  // Link comodo per l'operatore
                        'n_documents' => 0,
                    ],
                    // STEP 5: La Prova di Legge (Ricevuta Protocollo)
                    [
                        'ordine' => '50',
                        'name' => 'Ricevuta Protocollo OAM',
                        'item_code' => 'DOC_RICEVUTA_OAM',
                        'question' => "Allega il PDF della ricevuta di protocollo rilasciata dall'OAM a conferma della comunicazione.",
                        'description' => "Questo documento è l'unica prova legale in caso di ispezione per dimostrare il rispetto dei 10 giorni.",
                        'is_required' => 1,
                        'attach_model' => 'audit',  // Questo lo leghiamo all'audit compliance
                        'n_documents' => 1,
                    ],
                ];

                // Mappatura automatica dei campi default
                $formattedAgentItems = array_map(function ($item) use ($now, $checklistAgentId) {
                    return array_merge([
                        'checklist_id' => $checklistAgentId,
                        'answer' => null,
                        'annotation' => null,
                        'attach_model_id' => null,
                        'repeatable_code' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                        // Campi obbligatori della tabella checklist_items
                        'ordine' => $item['ordine'] ?? null,
                        'name' => $item['name'] ?? null,
                        'item_code' => $item['item_code'] ?? null,
                        'question' => $item['question'] ?? null,
                        'description' => $item['description'] ?? null,
                        'is_required' => $item['is_required'] ?? 0,
                        'n_documents' => $item['n_documents'] ?? 0,
                        'attach_model' => $item['attach_model'] ?? null,
                        'depends_on_code' => $item['depends_on_code'] ?? null,
                        'depends_on_value' => $item['depends_on_value'] ?? null,
                        'dependency_type' => $item['dependency_type'] ?? null,
                        'url_step' => $item['url_step'] ?? null,
                        'url_callback' => $item['url_callback'] ?? null,
                    ], $item);
                }, $itemsAgent);
                DB::table('checklist_items')->insert($formattedAgentItems);
            }
        }
    }
}
