<?php
namespace Database\Seeders;

use App\Models\DocumentScope;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Document Scopes
        $docScopes = [
            ['name' => 'Privacy', 'description' => 'GDPR Privacy Consent', 'color_code' => '#10B981'],
            ['name' => 'AML', 'description' => 'Anti-Money Laundering', 'color_code' => '#EF4444'],
            ['name' => 'OAM', 'description' => 'OAM Forms', 'color_code' => '#3B82F6'],
            ['name' => 'UIF', 'description' => 'UIF SOS', 'color_code' => '#3B82F6'],
            ['name' => 'Istruttoria', 'description' => 'Pratica docs', 'color_code' => '#F59E0B'],
            ['name' => 'Onboarding', 'description' => 'Onboarding', 'color_code' => '#F59E0B'],
            ['name' => 'Amministrativo', 'description' => 'Amministrativo', 'color_code' => '#F59E0B'],
        ];
        foreach ($docScopes as $scope) {
            DocumentScope::firstOrCreate(['name' => $scope['name']], $scope);
        }

        // 1. Recupero degli Scope
        $privacy = DocumentScope::where('name', 'Privacy')->first()?->id;
        $aml = DocumentScope::where('name', 'AML')->first()?->id;
        $oam = DocumentScope::where('name', 'OAM')->first()?->id;
        $uif = DocumentScope::where('name', 'UIF')->first()?->id;
        $istruttoria = DocumentScope::where('name', 'Istruttoria')->first()?->id;
        $onboarding = DocumentScope::where('name', 'Onboarding')->first()?->id;
        $amministrativo = DocumentScope::where('name', 'Amministrativo')->first()?->id;
        // 2. Definizione di tutti i 47 documenti con metadati tecnici
        // MODULISTICA E REDDITO
        $data = [
            // IDENTIFICAZIONE
            1 => ['name' => 'Carta di Identità', 'slug' => 'carta-identita', 'regex' => '/carta.*identit|c\.i\.|identit/i', 'scopes' => [$privacy, $aml, $istruttoria]],
            2 => ['name' => 'Patente di Guida', 'slug' => 'patente', 'regex' => '/patente/i', 'scopes' => [$privacy, $aml, $istruttoria]],
            3 => ['name' => 'Passaporto', 'slug' => 'passaporto', 'regex' => '/passaporto/i', 'scopes' => [$privacy, $aml, $istruttoria]],
            4 => ['name' => 'Codice Fiscale / Tessera Sanitaria', 'slug' => 'codice-fiscale', 'regex' => '/codice.*fiscale|tessera.*sanitaria|c\.f\./i', 'scopes' => [$privacy, $istruttoria]],
            // PRIVACY
            5 => ['name' => 'Informativa Privacy', 'slug' => 'privacy-informativa', 'regex' => '/informativa.*privacy|consenso.*dati/i', 'scopes' => [$privacy]],
            6 => ['name' => 'Consenso Dati Sanitari', 'slug' => 'consenso-sanitario', 'regex' => '/dati.*particolari|sanitari/i', 'scopes' => [$privacy]],
            7 => ['name' => 'Nomina Incaricato', 'slug' => 'nomina-incaricato', 'regex' => '/nomina.*incaricato/i', 'scopes' => [$privacy]],
            8 => ['name' => 'Nomina Responsabile', 'slug' => 'nomina-responsabile', 'regex' => '/nomina.*responsabile/i', 'scopes' => [$privacy]],
            9 => ['name' => 'Nomina Amministratore di Sistema', 'slug' => 'nomina-amministratore', 'regex' => '/amministratore.*sistema/i', 'scopes' => [$privacy]],
            // AML
            10 => ['name' => 'Questionario AML', 'slug' => 'questionario-aml', 'regex' => '/adeguata.*verifica|questionario.*aml/i', 'scopes' => [$aml]],
            11 => ['name' => 'Titolare Effettivo', 'slug' => 'titolare-effettivo', 'regex' => '/titolare.*effettivo/i', 'scopes' => [$aml]],
            12 => ['name' => 'Dichiarazione PEP', 'slug' => 'dichiarazione-pep', 'regex' => '/persona.*esposta.*politicamente|pep/i', 'scopes' => [$aml]],
            // MEDIAZIONE / TRASPARENZA
            13 => ['name' => 'Incarico di Mediazione', 'slug' => 'incarico-mediazione', 'regex' => '/lettera.*incarico|contratto.*mediazione/i', 'scopes' => [$oam]],
            14 => ['name' => 'Avviso di Trasparenza', 'slug' => 'trasparenza-avviso', 'regex' => '/avviso.*trasparenza|principali.*diritti/i', 'scopes' => [$oam]],
            15 => ['name' => 'Trasparenza Sito Web', 'slug' => 'trasparenza-web', 'regex' => '/trasparenza.*sito|foglio.*informativo/i', 'scopes' => [$oam]],
            16 => ['name' => 'Privacy Sito Web', 'slug' => 'privacy-web', 'regex' => '/privacy.*sito|privacy.*policy/i', 'scopes' => [$oam]],
            17 => ['name' => 'Requisiti Organizzativi Art. 6', 'slug' => 'requisiti-art6', 'regex' => '/requisiti.*organizzativi|art.*6/i', 'scopes' => [$oam]],
            // PROCEDURE OAM (18-35)
            18 => ['name' => 'Procedura Manuale Operativo', 'slug' => 'proc-manuale-operativo', 'regex' => '/manuale.*operativo/i', 'scopes' => [$oam]],
            19 => ['name' => 'Procedura Sistema Deleghe', 'slug' => 'proc-sistema-deleghe', 'regex' => '/sistema.*deleghe/i', 'scopes' => [$oam]],
            20 => ['name' => 'Procedura Compliance & Risk', 'slug' => 'proc-compliance-risk', 'regex' => '/compliance.*risk/i', 'scopes' => [$oam]],
            21 => ['name' => 'Procedura Internal Audit', 'slug' => 'proc-internal-audit', 'regex' => '/internal.*audit/i', 'scopes' => [$oam]],
            22 => ['name' => 'Procedura AML Verifica', 'slug' => 'proc-aml-verifica', 'regex' => '/verifica.*clientela/i', 'scopes' => [$oam, $aml]],
            23 => ['name' => 'Procedura AML Profilatura', 'slug' => 'proc-aml-profilatura', 'regex' => '/profilatura.*rischio/i', 'scopes' => [$oam, $aml]],
            24 => ['name' => 'Procedura AML SOS', 'slug' => 'proc-aml-sos', 'regex' => '/segnalazione.*sospette|sos/i', 'scopes' => [$oam, $aml]],
            25 => ['name' => 'Procedura Conservazione Dati', 'slug' => 'proc-aml-conservazione', 'regex' => '/conservazione.*dati/i', 'scopes' => [$oam, $aml]],
            26 => ['name' => 'Procedura Trasparenza Precontrattuale', 'slug' => 'proc-trasparenza-precontrattuale', 'regex' => '/informativa.*precontrattuale/i', 'scopes' => [$oam]],
            27 => ['name' => 'Procedura Controllo Pubblicità', 'slug' => 'proc-controllo-pubblicita', 'regex' => '/controllo.*pubblicit/i', 'scopes' => [$oam]],
            28 => ['name' => 'Procedura Gestione Reclami', 'slug' => 'proc-reclami-ricezione', 'regex' => '/ricezione.*trattazione.*reclami/i', 'scopes' => [$oam]],
            29 => ['name' => 'Informativa Reclami', 'slug' => 'proc-reclami-info', 'regex' => '/informativa.*risoluzione.*reclami/i', 'scopes' => [$oam]],
            30 => ['name' => 'Procedura Selezione Rete', 'slug' => 'proc-rete-selezione', 'regex' => '/selezione.*inserimento.*rete/i', 'scopes' => [$oam]],
            31 => ['name' => 'Procedura Formazione Rete', 'slug' => 'proc-rete-formazione', 'regex' => '/formazione.*continua/i', 'scopes' => [$oam]],
            32 => ['name' => 'Procedura Controlli Rete', 'slug' => 'proc-rete-controlli', 'regex' => '/controlli.*rete/i', 'scopes' => [$oam]],
            33 => ['name' => 'Procedura GDPR', 'slug' => 'proc-privacy-gdpr', 'regex' => '/gdpr.*data.*protection/i', 'scopes' => [$oam, $privacy]],
            34 => ['name' => 'Procedura Business Continuity', 'slug' => 'proc-business-continuity', 'regex' => '/business.*continuity|disaster.*recovery/i', 'scopes' => [$oam]],
            35 => ['name' => 'Modello 231 e Codice Etico', 'slug' => 'proc-231-etica', 'regex' => '/modello.*231|codice.*etico/i', 'scopes' => [$oam]],
            // MODULISTICA E REDDITO
            36 => ['name' => 'Modulo SECCI', 'slug' => 'modulo-secci', 'regex' => '/secci|informazioni.*europee/i', 'scopes' => [$oam, $istruttoria]],
            37 => ['name' => 'Segnalazione OAM', 'slug' => 'segnalazione-oam', 'regex' => '/segnalazione.*oam/i', 'scopes' => [$oam]],
            38 => ['name' => 'Buste Paga', 'slug' => 'buste-paga', 'regex' => '/busta.*paga/i', 'scopes' => [$istruttoria]],
            39 => ['name' => 'Certificazione Unica (CU)', 'slug' => 'cu', 'regex' => '/certificazione.*unica|modello.*cu/i', 'scopes' => [$istruttoria]],
            40 => ['name' => 'Certificato di Stipendio', 'slug' => 'certificato-stipendio', 'regex' => '/attestato.*servizio|certificato.*stipendio/i', 'scopes' => [$istruttoria]],
            41 => ['name' => 'Estratto Conto INPS', 'slug' => 'estratto-inps', 'regex' => '/estratto.*inps|contributivo/i', 'scopes' => [$istruttoria]],
            42 => ['name' => 'Cedolino Pensione', 'slug' => 'cedolino-pensione', 'regex' => '/cedolino.*pensione/i', 'scopes' => [$istruttoria]],
            43 => ['name' => 'Quota Cedibile', 'slug' => 'quota-cedibile', 'regex' => '/quota.*cedibile/i', 'scopes' => [$istruttoria]],
            44 => ['name' => 'Modello OBISM', 'slug' => 'modello-obism', 'regex' => '/obis/i', 'scopes' => [$istruttoria]],
            45 => ['name' => 'Conteggio Estintivo', 'slug' => 'conteggio-estintivo', 'regex' => '/conteggio.*estintivo/i', 'scopes' => [$istruttoria]],
            46 => ['name' => 'Visita Medica', 'slug' => 'visita-medica', 'regex' => '/visita.*medica/i', 'scopes' => [$istruttoria]],
            47 => ['name' => 'Documento Trasparenza TEGM', 'slug' => 'transparency-doc', 'regex' => '/rilevazione.*tassi|tassi.*usura|tegm/i', 'scopes' => [$oam]],
            // --- INTEGRAZIONI AREA COLLABORATORI (Onboarding & Admin) ---
            48 => ['name' => 'Visura Camerale', 'slug' => 'visura-camerale', 'regex' => '/visura.*camerale|camera.*commercio|registro.*imprese/i', 'scopes' => [$onboarding]],
            49 => ['name' => 'Casellario Giudiziale', 'slug' => 'casellario-giudiziale', 'regex' => '/casellario.*giudiziale|procura.*repubblica/i', 'scopes' => [$onboarding]],
            50 => ['name' => 'Carichi Pendenti', 'slug' => 'carichi-pendenti', 'regex' => '/carichi.*pendenti/i', 'scopes' => [$onboarding]],
            51 => ['name' => 'Attestato OAM / IVASS', 'slug' => 'attestato-professionale', 'regex' => '/attestato.*(oam|ivass)|prova.*valutativa|formazione.*professionale/i', 'scopes' => [$onboarding], 'is_agent' => true, 'is_principal' => true],  // Fuso con vecchio ID 63
            52 => ['name' => 'Polizza RC Professionale', 'slug' => 'polizza-rc', 'regex' => '/polizza.*rc|responsabilita.*civile|assicurativa/i', 'scopes' => [$onboarding], 'is_agent' => true, 'is_principal' => true],  // Fuso con vecchio ID 62
            53 => ['name' => 'Documento Identità e CF', 'slug' => 'identita-codice-fiscale', 'regex' => '/carta.*identita|passaporto|codice.*fiscale|tessera.*sanitaria/i', 'scopes' => [$onboarding], 'is_agent' => true, 'is_client' => true, 'is_principal' => true],  // Fuso con vecchio ID 59
            54 => ['name' => 'Modulo IBAN', 'slug' => 'iban-coordinate', 'regex' => '/iban|coordinate.*bancarie|appoggio.*conto/i', 'scopes' => [$amministrativo]],
            55 => ['name' => 'Contratto Collaborazione Firmato', 'slug' => 'contratto-firmato', 'regex' => '/contratto.*collaborazione|scrittura.*privata|accordo.*firmato/i', 'scopes' => [$amministrativo]],
            56 => ['name' => 'Autocertificazione Antimafia', 'slug' => 'antimafia', 'regex' => '/antimafia|dichiarazione.*sostitutiva/i', 'scopes' => [$onboarding]],
            57 => ['name' => 'Relazione Interna SOS', 'slug' => 'relazione-sos', 'regex' => '/relazione.*sos|analisi.*sospetta/i', 'scopes' => [$uif], 'is_agent' => true, 'is_principal' => true, 'is_company' => true],
            58 => ['name' => 'Ricevuta Portale UIF', 'slug' => 'ricevuta-sos-uif', 'regex' => '/ricevuta.*uif|infostat.*sos/i', 'scopes' => [$uif], 'is_agent' => true, 'is_principal' => true, 'is_company' => true],
            60 => ['name' => 'Contratto Collaborazione', 'slug' => 'contratto-agent', 'regex' => '/contratto.*collaborazione|scrittura.*privata/i', 'scopes' => [$amministrativo], 'is_agent' => true, 'is_company' => true],
            61 => ['name' => 'Regolamento Privacy', 'slug' => 'regolamento-privacy', 'regex' => '/privacy|gdpr|regolamento.*privacy/i', 'scopes' => [$privacy], 'is_company' => true],
            64 => ['name' => 'Modulo Richiesta Accesso Dati', 'slug' => 'richiesta-accesso-gdpr', 'regex' => '/richiesta.*accesso.*dati|esercizio.*diritti.*privacy/i', 'scopes' => [$privacy]],
            65 => ['name' => 'Riscontro al Cliente (GDPR)', 'slug' => 'riscontro-accesso-gdpr', 'regex' => '/riscontro.*accesso|invio.*dati.*personali/i', 'scopes' => [$privacy]],
            // --- INTEGRAZIONE VECCHIO ARRAY $documents ---
            'doc_1' => ['name' => 'Attestazione Ricevimento Documentazione Informativa', 'description' => 'Attestazione di ricevimento del foglio informativo e altra documentazione di trasparenza', 'slug' => 'attestazione-ricevimento-informativa', 'regex' => '/attestazione.*ricevimento.*(informativa|documentazione)|ricevuta.*informativa/i', 'is_person' => 1, 'is_client' => 1, 'is_practice' => 1],
            'doc_2' => ['name' => 'Fattura Pratica Mediazione', 'description' => 'Fatture relative alla pratica di mediazione', 'slug' => 'fattura-pratica-mediazione', 'regex' => '/fattura.*mediazione|fattura.*n\./i', 'is_person' => 1, 'is_client' => 1, 'is_practice' => 1],
            'doc_3' => ['name' => "Comunicazione Compenso all'Istituto", 'description' => "Comunicazione del compenso di mediazione all'Istituto erogante", 'slug' => 'comunicazione-compenso-istituto', 'regex' => '/comunicazione.*compenso|compenso.*mediazione/i', 'is_person' => 0, 'is_company' => 1, 'is_practice' => 1],
            'doc_4' => ['name' => 'Ricevuta Comunicazione Compenso', 'description' => "Attestazione di ricezione della comunicazione del compenso da parte dell'Istituto", 'slug' => 'ricevuta-comunicazione-compenso', 'regex' => '/ricezione.*comunicazione.*compenso|attestazione.*compenso/i', 'is_person' => 0, 'is_company' => 1, 'is_practice' => 1],
            'doc_5' => ['name' => 'Documentazione Servizi Congiunti (Consulenza)', 'description' => 'Documentazione relativa ai servizi offerti al cliente congiuntamente a quello di mediazione', 'slug' => 'documentazione-servizi-congiunti', 'regex' => '/servizi.*congiunti|contratto.*consulenza/i', 'is_person' => 1, 'is_client' => 1, 'is_practice' => 1],
            'doc_6' => ['name' => 'Modulo PIES', 'description' => 'Prospetto Informativo Europeo Standardizzato', 'slug' => 'modulo-pies', 'regex' => '/pies|prospetto.*informativo.*europeo/i', 'is_person' => 1, 'is_client' => 1, 'is_practice' => 1],
            'doc_7' => ['name' => 'Nota Descrittiva Intermediazione', 'description' => "Nota descrittiva sull'attività di intermediazione svolta dal collaboratore", 'slug' => 'nota-descrittiva-intermediazione', 'regex' => '/nota.*descrittiva.*intermediazione|attività.*svolta/i', 'is_person' => 1, 'is_agent' => 1, 'is_practice' => 1],
            'doc_8' => ['name' => 'Curriculum Vitae', 'description' => 'Curriculum vitae et studiorum per avvio mandato', 'slug' => 'curriculum-vitae', 'regex' => '/curriculum.*vitae|cv/i', 'is_person' => 1, 'is_agent' => 1, 'is_signed' => 0],
            'doc_9' => ['name' => 'Titolo di Studio', 'description' => 'Copia del titolo di studio o autocertificazione di possesso', 'slug' => 'titolo-di-studio', 'regex' => '/titolo.*studio|diploma|laurea/i', 'is_person' => 1, 'is_agent' => 1, 'is_signed' => 0],
            'doc_10' => ['name' => 'Certificato di Partita IVA', 'description' => 'Certificato di attribuzione Partita IVA', 'slug' => 'certificato-partita-iva', 'regex' => '/partita.*iva|attribuzione.*piva/i', 'is_company' => 0, 'is_person' => 1, 'is_agent' => 1, 'is_signed' => 0],
            'doc_11' => ['name' => 'Visura Cariche e Partecipazioni', 'description' => 'Visura camerale cariche e partecipazioni attive', 'slug' => 'visura-cariche-partecipazioni', 'regex' => '/visura.*cariche|partecipazioni/i', 'is_company' => 1, 'is_person' => 1, 'is_agent' => 1, 'is_signed' => 0],
            'doc_12' => ['name' => 'Prova Valutativa OAM', 'description' => 'Evidenza superamento prova valutativa OAM', 'slug' => 'prova-valutativa-oam', 'regex' => '/prova.*valutativa.*oam|superamento.*oam/i', 'is_person' => 1, 'is_agent' => 1, 'is_signed' => 0],
            'doc_13' => ['name' => 'Aggiornamento OAM', 'description' => 'Attestati di aggiornamento professionale OAM successivi', 'slug' => 'aggiornamento-oam', 'regex' => '/aggiornamento.*oam/i', 'is_person' => 1, 'is_agent' => 1, 'is_signed' => 0, 'is_monitored' => 1],
            'doc_14' => ['name' => 'Attestato Antiriciclaggio', 'description' => 'Attestato di formazione antiriciclaggio (se non in OAM)', 'slug' => 'formazione-antiriciclaggio', 'regex' => '/formazione.*antiriciclaggio|attestato.*aml/i', 'is_person' => 1, 'is_agent' => 1, 'is_signed' => 0, 'is_monitored' => 1],
            'doc_15' => ['name' => 'Tassa Concessione IVASS', 'description' => 'Versamento tassa di concessione governativa IVASS', 'slug' => 'tassa-concessione-ivass', 'regex' => '/tassa.*concessione.*ivass|tassa.*governativa/i', 'is_person' => 1, 'is_agent' => 1, 'is_signed' => 0],
            'doc_16' => ['name' => 'Evidenza Iscrizione IVASS', 'description' => 'Evidenza iscrizione al registro IVASS', 'slug' => 'iscrizione-ivass', 'regex' => '/iscrizione.*ivass|registro.*rui/i', 'is_person' => 1, 'is_agent' => 1, 'is_signed' => 0],
            'doc_17' => ['name' => 'Aggiornamento IVASS', 'description' => 'Attestati corso di aggiornamento IVASS successivi', 'slug' => 'aggiornamento-ivass', 'regex' => '/aggiornamento.*ivass/i', 'is_person' => 1, 'is_agent' => 1, 'is_signed' => 0, 'is_monitored' => 1]
        ];

        foreach ($data as $idOrKey => $attr) {
            // Preparazione dei dati del modello con fallback sicuri
            $modelData = [
                'name' => $attr['name'],
                'regex' => $attr['regex'],
                'description' => $attr['description'] ?? null,
                'priority' => $attr['priority'] ?? 1,
                'is_agent' => $attr['is_agent'] ?? false,
                'is_principal' => $attr['is_principal'] ?? false,
                'is_client' => $attr['is_client'] ?? false,
                'is_practice' => $attr['is_practice'] ?? ($attr['is_practice_target'] ?? false),
                'is_company' => $attr['is_company'] ?? false,
                'is_person' => $attr['is_person'] ?? false,
                'is_signed' => $attr['is_signed'] ?? false,
                'is_monitored' => $attr['is_monitored'] ?? false,
            ];

            // Forza l'ID se presente nell'array (chiavi numeriche da 1 a 65)
            if (is_int($idOrKey)) {
                $modelData['id'] = $idOrKey;
            }

            // UpdateOrCreate farà inserimento se lo slug non esiste, altrimenti aggiornerà i campi
            $type = DocumentType::updateOrCreate(
                ['slug' => $attr['slug']],
                $modelData
            );

            // Sincronizzazione Scopes se definiti nell'array
            if (isset($attr['scopes']) && !empty($attr['scopes'])) {
                $scopes = array_filter($attr['scopes']);
                if (!empty($scopes)) {
                    $type->scopes()->syncWithoutDetaching($scopes);
                }
            }
        }
    }
}
