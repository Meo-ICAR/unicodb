<?php

namespace Database\Seeders;

use App\Models\BusinessFunction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessFunctionSeeder extends Seeder
{
    public function run()
    {
        $funzioniAziendali = [
            [
                'code' => 'GOV-CDA',
                'macro_area' => 'Governance',
                'name' => 'Consiglio di Amministrazione / Direzione',
                'type' => 'Strategica',
                'description' => 'Definisce strategie, approva procedure organizzative, politiche di rischio e assicura l’adeguatezza dell’assetto organizzativo.',
                'outsourcable_status' => 'no',
            ],
            [
                'code' => 'BUS-DIRCOM',
                'macro_area' => 'Business / Commerciale',
                'name' => 'Direzione Commerciale',
                'type' => 'Operativa',
                'description' => 'Sviluppo accordi con Banche/Finanziarie, monitoraggio volumi e coordinamento Area Manager.',
                'outsourcable_status' => 'no',
            ],
            [
                'code' => 'BUS-RETE-GEST',
                'macro_area' => 'Business / Commerciale',
                'name' => 'Gestione Rete e Collaboratori',
                'type' => 'Operativa',
                'description' => 'Selezione, iscrizione elenchi OAM e monitoraggio dell’operato dei collaboratori esterni.',
                'outsourcable_status' => 'no',
            ],
            [
                'code' => 'BUS-RETE-EXT',
                'macro_area' => 'Business / Commerciale',
                'name' => 'Gestione Rete e Collaboratori',  // Nota: Nome enum uguale ma code diverso
                'type' => 'Operativa',
                'description' => 'Agenti e collaboratori sul territorio: vendita, relazione cliente e raccolta documentale primaria.',
                'outsourcable_status' => 'no',
            ],
            [
                'code' => 'BUS-BO',
                'macro_area' => 'Business / Commerciale',
                'name' => 'Back Office / Istruttoria Pratiche',
                'type' => 'Operativa',
                'description' => 'Istruttoria, controlli di I livello, caricamento portali bancari e gestione benestari CQS.',
                'outsourcable_status' => 'partial',
            ],
            [
                'code' => 'SUP-AMM',
                'macro_area' => 'Supporto',
                'name' => 'Amministrazione e Contabilità',
                'type' => 'Supporto',
                'description' => 'Contabilità, fatturazione provvigioni attive/passive e gestione flussi finanziari.',
                'outsourcable_status' => 'yes',
            ],
            [
                'code' => 'SUP-IT',
                'macro_area' => 'Supporto',
                'name' => 'IT e Sicurezza Dati',
                'type' => 'Supporto',
                'description' => 'Gestione CRM, sicurezza informatica e continuità operativa.',
                'outsourcable_status' => 'yes',
            ],
            [
                'code' => 'SUP-RECLAMI',
                'macro_area' => 'Supporto',
                'name' => 'Gestione Reclami e Controversie',
                'type' => 'Supporto',
                'description' => 'Analisi reclami, gestione ricorsi ABF e reporting per la Direzione.',
                'outsourcable_status' => 'yes',
            ],
            [
                'code' => 'CTRL-COMPL',
                'macro_area' => 'Controlli (II Livello)',
                'name' => 'Compliance (Conformità)',
                'type' => 'Controllo',
                'description' => 'Prevenzione del rischio di non conformità normativa (Trasparenza, OAM, Privacy).',
                'outsourcable_status' => 'yes',
            ],
            [
                'code' => 'CTRL-AML',
                'macro_area' => 'Controlli (II Livello)',
                'name' => 'Antiriciclaggio (AML)',
                'type' => 'Controllo',
                'description' => 'Profilatura rischio, tenuta AUI, analisi operazioni sospette e segnalazioni SOS.',
                'outsourcable_status' => 'yes',
            ],
            [
                'code' => 'CTRL-AUDIT',
                'macro_area' => 'Controlli (III Livello)',
                'name' => 'Internal Audit (Revisione Interna)',
                'type' => 'Controllo',
                'description' => 'Ispezioni indipendenti e test a campione su tutto l’impianto organizzativo.',
                'outsourcable_status' => 'yes',
            ],
        ];

        foreach ($funzioniAziendali as $item) {
            BusinessFunction::updateOrCreate(
                ['code' => $item['code']],  // Usa il code come chiave per evitare duplicati
                $item
            );
        }

        $now = Carbon::now();

        $roles = [
            [
                'code' => 'CTRL-AUDIT',
                'name' => 'Internal Audit (Revisione Interna)',
                'managed_by_code' => 'GOV-CDA',  // Consiglio di Amministrazione
                'mission' => "Garantire la corretta amministrazione dell’Azienda nel pieno rispetto delle norme di legge, regolamentari e statutarie, della Relazione sui Requisiti Organizzativi.\nGarantisce la completezza, l’adeguatezza, la funzionalità e l’affidabilità del sistema dei controlli interni e del sistema informativo.\nGarantire il controllo su tutte le attività aziendali secondo quanto disposto dalla normativa vigente.",
                'responsibility' => "• Esamina processi, procedure, protocolli decisionali, centri di responsabilità e, più in generale, l’operatività interna.\n• Conduce ricognizioni dell’attività aziendale ai fini della mappatura delle attività sensibili.\n• Garantisce un accurato operative reporting.\n• Pone in essere le condizioni per la costante massimizzazione dell’efficacia.\n• Predispone periodicamente reports sulle attività a rischio.\n• Controlla e garantisce le attività operative derivanti dagli adempimenti normativi.\n• Effettua e garantisce le attività di monitoraggio.\n• Provvede alla redazione degli schemi di reporting per il controllo delle attività a rischio.\n• Sviluppa metodi di conduzione della revisione interna.\n• Controlla l’affidabilità dei sistemi informativi e di rilevazione contabile.\n• Verifica la rimozione delle anomalie riscontrate.\n• Monitora la corretta applicazione ed il pieno rispetto della normativa interna.",
            ],
            [
                'code' => 'CTRL-AML',
                'name' => 'Antiriciclaggio (AML)',
                'managed_by_code' => 'GOV-CDA',  // Consiglio di Amministrazione
                'mission' => 'Garantire gli adempimenti previsti in materia di antiriciclaggio, secondo quanto previsto dalla normativa vigente, assicurando adeguati livelli di servizio.',
                'responsibility' => "• Garantisce ogni forma di supporto legale all’azienda fornendo pareri tecnici.\n• Difende l’operato e gli interessi dell’Azienda per le proprie competenze.\n• Cura gli adempimenti previsti dalla normativa antiriciclaggio.\n• Segnala all’Amministratore Delegato eventuali anomalie riscontrate in materia di antiriciclaggio.\n• È il Responsabile aziendale dell’antiriciclaggio.\n• Promuove una cultura aziendale improntata a principi di onestà, correttezza e rispetto assoluto delle norme.\n• Propone specifici presidi organizzativi volti ad assicurare il rigoroso rispetto delle prescrizioni normative.\n• Esamina, con continuità, le norme applicabili all’Azienda, misurandone e valutandone l’impatto.",
            ],
            [
                'code' => 'CTRL-DPO',
                'name' => 'Data Protection Officer (DPO)',
                'managed_by_code' => 'GOV-CEO',  // Amministratore Delegato
                'mission' => "Assicurare la predisposizione [e gli eventuali aggiornamenti] del Regolamento Privacy e del Registro delle Attività di Trattamento.\nDefinire le metodologie di misurazione dei rischi in materia di privacy.\nValutare l’impatto della normativa in materia di privacy.",
                'responsibility' => "• Individua i rischi rilevanti e le relative fonti di generazione in materia di Privacy.\n• Monitora costantemente l’evoluzione dei rischi aziendali ed il rispetto dei limiti operativi.\n• Analizza i rischi di eventuali nuovi prodotti e l’ingresso in nuovi segmenti in materia di Privacy.\n• Verifica nel continuo l’adeguatezza del processo di gestione dei rischi e dei relativi limiti operativi in materia di Privacy.\n• Sviluppa e mantiene sistemi di misurazione e controllo dei rischi ed indicatori di anomalie.\n• Verifica l’adeguatezza e l’efficacia delle misure adottate per rimediare alle carenze riscontrate.",
            ],
            [
                'code' => 'SUP-LEG-AMM',
                'name' => 'Amministrazione e Contabilità',
                'managed_by_code' => 'GOV-CEO',  // Amministratore Delegato
                'mission' => "Assistere l’Azienda su ogni problematica attinente la propria sfera di competenza.\nAssicurare gli adempimenti societari ed il supporto costante al Consiglio di Amministrazione per le attività di segreteria Societaria, garantendo correttezza e legittimità formale.\nRilevazione dei fatti gestionali, generazione del bilancio d’esercizio annuale.\nGarantire la tempestiva e corretta registrazione contabile dei movimenti ai fini della predisposizione del bilancio.",
                'responsibility' => "• Fornisce supporto all’Azienda in materia di recupero dei crediti vantati.\n• Garantisce supporto legale fornendo pareri tecnici ed indicazioni operative.\n• Assicura ogni attività di natura giuridico-legale derivante da pignoramenti, vincoli, sequestri.\n• Gestisce le polizze assicurative e i relativi rapporti.\n• Difende l’operato e gli interessi dell’Azienda.\n• Predispone flussi informativi alle strutture interessate.\n• Supporto al legale incaricato all’avvio di azioni giudiziarie.\n• Gestione reclami e ricorsi ABF.\n• Gestione del precontenzioso e contenzioso.\n• Adempimenti contabili e fiscali, fatturazione attiva/passiva, chiusura contabile mensile e predisposizione bilancio.",
            ],
            [
                'code' => 'CTRL-COMPL',
                'name' => 'Compliance (Conformità)',
                'managed_by_code' => 'GOV-CEO',  // Amministratore Delegato
                'mission' => "Assicurare la predisposizione del Regolamento della Funzione di Compliance.\nGarantire il rispetto della legalità e della correttezza negli affari.\nAssistere l’Azienda su ogni problematica attinente la propria sfera di competenza.",
                'responsibility' => "• Formalizzazione nel documento di pianificazione annuale (Compliance Plan) dei controlli da svolgere.\n• Rappresentazione agli organi aziendali degli esiti delle attività svolte.\n• Consulenza e assistenza nei confronti degli organi aziendali in tema di rischio di non conformità.\n• Identificazione nel continuo delle norme applicabili e misurazione impatto su processi aziendali.\n• Partecipazione al processo di validazione delle procedure aziendali.\n• Effettuazione di test periodici sul funzionamento delle procedure operative e di controllo.\n• Predisposizione di flussi informativi diretti al Responsabile delle Funzione Compliance.\n• Supporto nella predisposizione della Relazione annuale e Piano annuale di Compliance.\n• Promuove una cultura aziendale di onestà e correttezza.\n• Verifica l'efficacia degli adeguamenti organizzativi per prevenire il rischio di conformità.",
            ],
            [
                'code' => 'SUP-ORG',
                'name' => 'Risorse Umane (HR) e Formazione',
                'managed_by_code' => 'GOV-CEO',  // Amministratore Delegato
                'mission' => "Elaborare la composizione più conveniente delle forze personali, materiali e immateriali operanti in Azienda.\nPredisposizione e manutenzione dell’impianto documentale aziendale.\nEseguire una efficiente ed efficace gestione del personale.",
                'responsibility' => "• Predisposizione e manutenzione delle policy, dei processi e delle attività aziendali.\n• Pianificazione e gestione interventi per adeguamento normativo.\n• Gestisce le attività inerenti la logistica.\n• Fornire pareri sull'organico da reclutare.\n• Assicurare l’adeguatezza quali-quantitativa delle risorse umane e rispetto CCNL.\n• Gestire il personale, le rilevazioni, relazioni sindacali e contrattazioni.\n• Fornire l’andamento periodico del costo del personale per il budget.\n• Individua referenti aziendali per attività in outsourcing e gestisce acquisti.",
            ],
            [
                'code' => 'SUP-PLAN',
                'name' => 'Marketing e Comunicazione',
                'managed_by_code' => 'GOV-CEO',  // Amministratore Delegato (CEO)
                'mission' => "Garantire il processo di pianificazione strategica, di controllo della gestione e di valutazione economico reddituale delle opportunità di business.\nFornire un contributo all’Amministratore Delegato per la valutazione delle scelte strategiche aziendali.",
                'responsibility' => "• Garantisce all’Amministratore Delegato i flussi informativi necessari sui fatti di gestione rilevanti.\n• Collabora nella formulazione e condivisione del Piano Strategico dell’Azienda.\n• Garantisce la predisposizione di report andamentali periodici.\n• Garantisce assistenza per la corretta predisposizione dei budget, revisioni e forecasting.\n• Svolge l'analisi degli scostamenti tra budget e dati consuntivi, evidenziando le criticità.\n• Predispone i prospetti di bilancio e assicura la corretta registrazione degli eventi contabili.",
            ],
            [
                'code' => 'BUS-DIRCOM',
                'name' => 'Direzione Commerciale',
                'managed_by_code' => 'GOV-CEO',  // Amministratore Delegato [CEO]
                'mission' => "Garantire, in coerenza con le strategie aziendali, il raggiungimento degli obiettivi di produzione, di redditività e di rischio nei confronti dei convenzionati.\nAssicurare la gestione, l’animazione e l’assistenza ai convenzionati.\nGarantire il coordinamento operativo delle risorse allocate sulla Rete Commerciale.\nGarantire un adeguato supporto analitico e quantitativo.",
                'responsibility' => "• Supervisiona il raggiungimento degli obiettivi di produzione e redditività stabiliti a livello strategico.\n• Gestisce e anima le risorse della rete definendo le priorità di intervento.\n• Coordina l'operatività commerciale in conformità al modello di business dell'Azienda.",
            ]
        ];

        foreach ($roles as $item) {
            BusinessFunction::updateOrCreate(
                ['code' => $item['code']],  // Usa il code come chiave per evitare duplicati
                $item
            );
        }
    }
}
