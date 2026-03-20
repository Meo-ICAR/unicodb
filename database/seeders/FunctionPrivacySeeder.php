<?php
namespace Database\Seeders;

use App\Models\CompanyFunction;
use App\Models\FunctionPrivacy;
use Illuminate\Database\Seeder;

class FunctionPrivacySeeder extends Seeder
{
    public function run()
    {
        // Recupero i reparti chiave
        $backOffice = CompanyFunction::where('code', 'BUS-BO')->first();
        $aml = CompanyFunction::where('code', 'CTRL-AML')->first();
        $hr = CompanyFunction::where('code', 'SUP-HR')->first();

        $vociRegistro = [];

        // 1. Scheda per il BACK OFFICE (Core Business)
        if ($backOffice) {
            $vociRegistro[] = [
                'function_id' => $backOffice->id,
                'processing_activity' => 'Istruttoria e intermediazione pratiche di credito',
                'data_subjects' => 'Clienti (Richiedenti, Garanti)',
                'data_categories' => 'Dati anagrafici, di contatto, documenti di identità, dati reddituali e patrimoniali, dati bancari. Eventuali dati sanitari (solo per polizze connesse).',
                'purpose' => "Valutazione del merito creditizio, raccolta documentazione e inoltro della richiesta di finanziamento all'istituto di credito.",
                'legal_basis' => 'Esecuzione di un contratto',
                'recipients' => 'Banche, Finanziarie convenzionate, Compagnie Assicurative',
                'non_eu_transfer' => 'Nessuno',
                'retention_period' => '10 anni dalla chiusura della pratica (Art. 2220 c.c.)',
                'security_measures' => 'Accesso tramite VPN, database crittografato, profili di autorizzazione rigidi.',
                'is_active' => true,
            ];
        }

        // 2. Scheda per l'ANTIRICICLAGGIO (Obbligo Normativo)
        if ($aml) {
            $vociRegistro[] = [
                'function_id' => $aml->id,
                'processing_activity' => 'Adeguata Verifica della Clientela (AML)',
                'data_subjects' => 'Clienti, Titolari Effettivi, Esecutori',
                'data_categories' => "Dati anagrafici, documenti d'identità, informazioni su scopo e natura del rapporto, informazioni su esposizione politica (PEP).",
                'purpose' => 'Adempimento degli obblighi previsti dal D.Lgs. 231/2007 in materia di prevenzione del riciclaggio e finanziamento del terrorismo.',
                'legal_basis' => 'Obbligo di legge',
                'recipients' => 'UIF (Unità di Informazione Finanziaria), Autorità Giudiziaria',
                'non_eu_transfer' => 'Nessuno',
                'retention_period' => '10 anni dalla fine del rapporto continuativo (D.Lgs. 231/07)',
                'security_measures' => 'Fascicoli digitali ad accesso ristretto al solo personale autorizzato AML.',
                'is_active' => true,
            ];
        }

        // 3. Scheda per le RISORSE UMANE (Gestione Dipendenti e Rete)
        if ($hr) {
            $vociRegistro[] = [
                'function_id' => $hr->id,
                'processing_activity' => 'Gestione amministrativa del personale e collaboratori',
                'data_subjects' => 'Dipendenti, Collaboratori OAM, Candidati',
                'data_categories' => 'Dati anagrafici, curriculum vitae, coordinate bancarie (IBAN), certificati medici (malattia/infortunio), casellario giudiziale (per OAM).',
                'purpose' => 'Gestione del rapporto di lavoro subordinato o di collaborazione agenziale, elaborazione buste paga/provvigioni, iscrizione elenchi OAM.',
                'legal_basis' => 'Esecuzione di un contratto',
                'recipients' => 'Consulente del Lavoro (Esterno), OAM, INPS, INAIL, Agenzia delle Entrate',
                'non_eu_transfer' => 'Nessuno',
                'retention_period' => '10 anni dalla cessazione del rapporto contrattuale',
                'security_measures' => 'Armadi chiusi a chiave per il cartaceo, cartelle server con permessi limitati al solo ufficio HR.',
                'is_active' => true,
            ];
        }

        // Inserimento a database
        foreach ($vociRegistro as $voce) {
            FunctionPrivacy::create($voce);
        }
    }
}
