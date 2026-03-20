<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Company;
use App\Models\Principal;
use App\Models\PrincipalContact;
use App\Models\PrincipalMandate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoreDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company)
            return;

        // Update company with numero_iscrizione_rui default value
        $company->update([
            'numero_iscrizione_rui' => 'E000689226',
            'oam' => 'M510'
        ]);

        // Mappa di lookup: Parola chiave -> Codice ABI/Identificativo
        $lookup = [
            'FINDOMESTIC' => '03110', 'IBL' => '03263', 'SANTANDER' => '03191', 'SSNTANDER' => '03191',
            'PROGETTO' => '05015', 'PROGETO' => '05015', 'COMPASS' => '03069', 'FUCINO' => '03124',
            'VIVIBANCA' => '03431', 'DYNAMICA' => '13144', 'DINAMICA' => '13144', 'DINAMIKA' => '13144',
            'SCONTO' => '03347', 'CREDITIS' => '19451', 'BNL' => '01005', 'FINCONTINUO' => '33182',
            'UNICREDIT' => '02008', 'UNIDEBIT' => '02008', 'AGOS' => '03050', 'SISTEMA' => '03159',
            'FIDITALIA' => '03313', 'FIDES' => '19500', 'CHEBANCA' => '03058', 'PRESTITALIA' => '03206',
            'PRESTIITALIA' => '03206', 'SPEFIN' => '13264', 'PITAGORA' => '13318', 'PREXTA' => '35000',
            'COFIDIS' => '32537', 'ITALCREDI' => '33156', 'ITALECREDI' => '33156', 'DEUTSCHE' => '03032',
            'DEUTSHE' => '03032', 'DEUTUSCHE' => '03032', 'MCE' => '36061', 'BIBANCA' => '03055',
            'BPER' => '05387', 'EUROCQS' => '33221', 'MIKRO' => '19614', 'CF+' => '03202',
            'FIDIT' => '19273', 'CONFESERFIDI' => '19307', 'GUBER' => '03667', 'PERMICRO' => '19532',
            'AVVERA' => '03032', 'PUGLIA E BASILICATA' => '05830', 'ADV' => '36070', 'NUOVA TERRA' => '03417',
            'CAPITALFIN' => '33062', 'IFIS' => '03205', 'YOUNITED' => '03651', 'INTESA' => '03069',
            'PROFAMILY' => '03341', 'SELLA' => '03511', 'VITANUOVA' => 'B000523030', 'ISIDE' => 'B000401869',
            'SIGLA' => '13344', 'VENETO ROMAGNOLO' => '19196', 'CA AUTO' => '03674', 'FINANCIT' => '33303',
            'FINACIT' => '33303', 'BDM' => '05424', 'IMPREBANCA' => '03332', 'PUGLIESE' => '05262',
            'BIEFFE' => '33145', 'FLUMERI' => '08544', 'TOWERS' => '36104', 'IFIVER' => '19520',
            'IBL FAMILY' => '36102', 'CREDEM' => '03032', 'CARIGE' => '06175', 'SONDRIO' => '05696',
            'SASSARI' => '01015', 'LEASING' => '19459', 'CONAFI' => '13331', 'FUTURO' => '03277',
            'ING BANK' => '03475', 'POSTE' => '36081', 'MONTEPASCHI' => '01030', 'FERCREDIT' => '13312',
            'FIGENPA' => '13328', 'FINGENPA' => '13328', 'FINGEPA' => '13328', 'CENTRO FINANZIAMENTI' => '33221'
        ];

        $rawNames = [
            'ACCEDO', 'ADV', 'ADV Finance', 'AGENZIA DELLE ENTRATE', 'AGENZIA ENTRATE', 'AGOS', 'AGOS DUCATO', 'AGOS DUCATO SPA',
            'AGOS SPA', 'ANDSAI',
            'ASSICURAPOINT', 'Assicurazione', 'ATLANDIDE', 'AVVERA', 'Avvera SPA', 'BANCA  DI  SCONTO', 'BANCA  PROGETTO',
            'BANCA CF+', 'BANCA CREDITO LOMBARDO', 'banca credito popolare', 'BANCA DEL CREDITO POPOLARE', 'BANCA DEL FUCINO',
            'BANCA DI  SCONTO', 'BANCA DI CREDITO POPOLARE', 'BANCA DI CREDITO POPOLARE SOC. COOP', 'BANCA DI CREDITO POPOLARE SOC.COOP.',
            'BANCA DI SASSARI', 'BANCA DI SCONTO', 'BANCA DI SCONTO - IBL FAMILY', 'BANCA DI SCONTO SPA', 'BANCA GUBER',
            'BANCA IFIS', 'BANCA INTESA', 'BANCA NAZIONALE DEL LAVORO', 'BANCA NUOVA TERRA', 'BANCA POP PUGLIESE',
            'BANCA POPOLARE DI PUGLIA E BASILICATA', 'BANCA POPOLARE DI SONDRIO', 'BANCA POPOLARE PUGLIESE', 'BANCA PRIVATA LEASING',
            'BANCA PRIVATA LEASING SPA', 'BANCA PROGETO', 'BANCA PROGETTO', 'Banca Progetto Spa', 'banca sella',
            'Banca Sella Personal Credit', 'BANCA SISTEMA', 'BANCA SISTEMA S.P.A.', 'BANCA SISTEMA SPA', 'BCC FLUMERI',
            'BDM', 'BDM - PRS', 'BHO', 'BI BANCA', 'BIBANCA', 'BIBANCA SPA', 'BIBIBANCA SPA', 'BIEFFE 5', 'BLUE FACTOR SPA',
            'BNL', 'BNL - CRP', 'BNL BANCA', 'BNL FINANCE', 'BNL FINANCE SPA', 'BNT', 'BNT BANCA', 'BPER', 'BPER BANCA SPA',
            'BPL/PITAGORA', 'BPP SVILUPPO SPA', 'CA AUTO BANK', 'CAPITAL.FIN', 'CAPITALFIN', 'CARIGE', 'CARIGE / BPER BANCA',
            'CASA MUTUA PERSOCIV MIN. DIFESA', 'CASH ME', 'CASSA DI PREVIDENZA DELLE FORZE ARMATE', 'CEMTRO FINANZIAMENTI',
            'CENTRO FINANZIAM ENTI', 'CENTRO FINANZIAMENTI', 'CENTRO FINANZIAMENTI SPA', 'CERCHECCI FRANCESCO', 'CHEBANCA - CRP',
            'CHEBANCA - PRS', 'CHEBANCA! SPA', 'COFIDIS', 'compass', 'COMPASS BANCA S.P.A', 'Compass Banca Spa', "CONAFI PRESTITO'",
            "CONAFI PRESTITO' SPA", 'Condominio & Dintorni Srl', 'CONFESERFIDI', 'CONSEL', 'CREDEM', 'CREDEM SPA', 'CREDIPER QUINTO',
            'CREDIT AGRICOLE - PRS', 'CREDIT FACTOR', 'CREDITIS', 'CREDITIS SERVIZI FINANZIARI', 'CREDITIS SPA', 'CREDITO LOMBARDO',
            'Credito Meridionale', 'DA VERIFICARE', 'de luca fernanda', 'deutch bank', 'DEUTSCHE BANK', 'DEUTSCHE BANK SPA',
            'DEUTSHE BANK', 'DEUTUSCHE', 'dinamica retails', 'dinamica retaily', 'DINAMIKA', 'DYNAMICA', 'DYNAMICA RETAIL',
            'DYNAMICA RETAIL SPA', 'EMERGENZA DEBITI SRL', 'EUROCQS', 'FERCREDIT', 'FIDES', 'FIDES SPA', 'FIDIT', 'FIDITALIA',
            'FIDITALIA SPA', 'FIGENPA', 'FIGENPA SPA', 'FIN ABRUZZO', 'finacit', 'FINANCIT', 'FINANCIT S.p.a.', 'Financit SPA',
            'FINCONTINUO', 'FINCONTINUO SPA', 'FINDOMESTIC', 'FINDOMESTIC BANCA', 'Findomestic Banca Spa', 'FINDOMESTIC SPA',
            'FINGENPA', 'FINGEPA', 'FINITALIA', 'FUCINO FINANCE', 'FUTURO', 'FUTURO SPA', 'FUTURO/COMPASS', 'GIORDI SRL',
            'I.FI.VE.R SPA', 'IBL', 'IBL  BANCA', 'IBL BANCA', 'IBL Banca Spa', 'IBL FAMILY', 'IBL FAMILY S.P.A.', 'IBL FAMILY SPA',
            'IBL FAMILY/BDS', 'IFIN NPL SPA', 'IFIS NPL', 'IFIVER', 'IFIVER SPA', 'IMPREBANCA Spa', 'ING - CENTRO FINANZIAMENTI (EX RACES)',
            'ING BANK', 'ING BANK - PRS', 'INPDAP', 'INPS', 'INPS EX INPDAP', 'INTESA SAN PAOLO', 'INTESA SAN PAOLO SPA',
            'INTESA SANPAOLO', 'INTESA SPA', 'Iside Broker', 'Ist. Fin. Veneto Romagnolo Spa', 'ISTITUTO FUORI CONVENZIONE',
            'ISTITUTO GONZAGA DI MILANO', 'ITALCAPITAL SRL', 'ITALCREDI', 'ITALCREDI SPA', 'ITALECREDI', 'MARATHON SPV SRL',
            'marte spv', 'MB CREDIT SOLUTION', 'mce', 'MCE FINACE', 'mce finance', 'MCE FINANCE SPA', 'MIKRO KAPITAL', 'MONTEPASCHI',
            'NON DEFINITO ANCORA', 'PERMICRO', 'PIG.TO', 'PIGN. A TERZI', 'PIGN.TO', 'PIGN.TO A TERZI', 'PIGNORAMENTO',
            'PIGNORAMENTO A TERZI', 'PIGNORAMENTO AG. ENTRATE', 'PIGNORAMENTO FINDOMESTIC', 'PIGNORAMENTO PER ALIMENTI',
            'PIGNORAMENTO VERSO TERZI', 'PITAGORA', 'PITAGORA SPA', 'PITAGORA\\', 'POSTE BNL', 'PP AZIENDALE', 'PP INPDAP',
            'prestiitalia', 'PRESTITALIA', 'Prestitalia Spa', 'PRESTITEMPO', 'PRESTITO AZIENDALE', 'PRESTITO INTERNO', 'prexta',
            'PREXTA SPA', 'PRIVATO', 'PROFAMILY', 'Quinto puoi', 'RACES', 'RECUPERO OBBLIGATORIO', 'SANTANDER', 'SANTANDER BANK',
            'SANTANDER CONSUME BANK', 'SANTANDER CONSUMER', 'SANTANDER CONSUMER BANK', 'Santander Consumer Bank Spa',
            'SANTANDER NSUMER BANK', 'Sella Personal Credit', 'SELLA PERSONAL CREDIT SPA', 'SIGLA', 'SIGLA CREDIT',
            'SIGLA CREDIT SRL', 'Sigla Srl', 'SIRIOFIN', 'SISTEMA BANCARIO', "SOCIETA'DI MUTUO SOCCORSO", 'SOGET SPA',
            'SPEFIN', 'Spefin finanziaria', 'SPEFIN FINANZIARIA SPA', 'spefin spa', 'Ssntander', 'SWITCHO', 'TERFINACE',
            'TOWERS CQ', 'TRASPORTI E MAGAZINI SRL', 'UNICREDIT', 'UNICREDIT BANCA SPA', 'UNICREDIT SPA', 'UNIDEBIT', 'UNIPOL ASS.',
            'V-CENTRO FINANZIAMENTI SPA', 'VITANUOVA', 'Vitanuova spa', 'VITTORIA ASSICURAZIONI', 'Vivi Banca', 'VIVIBANCA',
            'VIVIBANCA SPA', 'we finance', 'WE FINANCE SPA', 'Younited', 'YOUNITED SA'
        ];

        foreach ($rawNames as $name) {
            $abi = null;
            $upperName = strtoupper($name);

            foreach ($lookup as $key => $code) {
                if (str_contains($upperName, $key)) {
                    $abi = $code;
                    break;
                }
            }

            /*
             * DB::table('principals')->insert([
             *     'name' => $name,
             *     'abi' => $abi,
             *     'company_id' => $company->id,
             *     'principal_type' => (str_contains($upperName, 'ASSICURA') || str_contains($upperName, 'VITANUOVA') || str_contains($upperName, 'ISIDE')) ? 'agente_assicurativo' : 'banca',
             *     'is_active' => 1,
             *     'status' => 'ATTIVO',
             *     'created_at' => now(),
             *     'updated_at' => now(),
             * ]);
             */
        }

        // Agents (Rete Commerciale Esterna)
        $agents = [
            [
                'name' => 'Eurofinanza Mediazioni SRL',
                'description' => 'Mediatore Creditizio Nazionale',
                'oam' => 'M456',  // Formato tipico mediatori (M + numero)
                'type' => 'Mediatore',
            ],
            [
                'name' => 'Mario Rossi Consulenze',
                'description' => 'Agente in Attività Finanziaria',
                'oam' => 'A1234',  // Formato tipico agenti (A + numero)
                'type' => 'Agente',
            ],
            [
                'name' => 'Rete Prestiti Direct SPA',
                'description' => 'Partner Territoriale CQS',
                'oam' => 'M987',
                'type' => 'Mediatore',
            ],
            [
                'name' => 'Studio Finanziario Bianchi SAS',
                'description' => 'Agenzia Specializzata Pensionati',
                'oam' => 'A5566',
                'type' => 'Agente',
            ],
        ];

        /*
         * foreach ($agents as $agentData) {
         *     Agent::firstOrCreate(
         *         ['name' => $agentData['name'], 'company_id' => $company->id],
         *         [
         *             'description' => $agentData['description'],
         *             'oam' => $agentData['oam'],
         *             'type' => $agentData['type'],
         *             'is_active' => 1
         *         ]
         *     );
         * }
         */
    }
}
