<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OamScopeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $oamScopes = [
            ['code' => 'A.1', 'name' => 'Mutui'],
            ['code' => 'A.2', 'name' => 'Cessioni del V dello stipendio/pensione e delegazioni di pagamento', 'tipo_prodotto' => 'Cessione'],
            ['code' => 'A.3', 'name' => 'Factoring crediti', 'tipo_prodotto' => 'Factoring'],
            ['code' => 'A.4', 'name' => 'Acquisto di crediti', 'tipo_prodotto' => 'Acquisto'],
            ['code' => 'A.5', 'name' => 'Leasing autoveicoli e aeronavali', 'tipo_prodotto' => 'Leasing'],
            ['code' => 'A.6', 'name' => 'Leasing immobiliare', 'tipo_prodotto' => 'Leasing'],
            ['code' => 'A.7', 'name' => 'Leasing strumentale', 'tipo_prodotto' => 'Leasing'],
            ['code' => 'A.8', 'name' => 'Leasing su fonti rinnovabili ed altre tipologie di investimento'],
            ['code' => 'A.9', 'name' => 'Aperture di credito in conto corrente'],
            ['code' => 'A.10', 'name' => 'Credito personale'],
            ['code' => 'A.11', 'name' => 'Credito finalizzato'],
            ['code' => 'A.12', 'name' => 'Prestito su pegno'],
            ['code' => 'A.13', 'name' => 'Rilascio di fidejussioni e garanzie'],
            ['code' => 'A.13-bis', 'name' => 'Garanzia collettiva dei fidi'],
            ['code' => 'A.14', 'name' => 'Anticipi e sconti commerciali'],
            ['code' => 'A.15', 'name' => 'Credito revolving'],
            ['code' => 'A.16', 'name' => 'Ristrutturazione dei crediti (art. 128-quater decies, del TUB)'],
            ['code' => 'Consulenza', 'name' => ' '],
            ['code' => 'Segnalazione mutuo', 'name' => ' '],
        ];

        foreach ($oamScopes as $scope) {
            \App\Models\OamScope::firstOrCreate([
                'code' => $scope['code']
            ], [
                'name' => $scope['name'],
                'description' => $scope['code'] . ' ' . $scope['name'] ?? null,
            ]);
        }

        $this->command->info('OAM Scopes seeded successfully');

        $practices = [
            // --- MUTUI ---
            ['id' => 1, 'name' => 'Mutui', 'code' => 'MUT', 'oam_code' => 'A.1', 'tipo_prodotto' => 'Mutuo', 'is_oneclient' => 0],
            ['id' => 34, 'name' => 'IPOTECARIO', 'code' => 'MUT_IPOTECARIO', 'oam_code' => 'A.1', 'tipo_prodotto' => 'Mutuo', 'is_oneclient' => 0],
            // --- CESSIONI ---
            ['id' => 2, 'name' => 'Cessioni del V dello stipendio', 'code' => 'CessioneCQS', 'oam_code' => 'A.2', 'tipo_prodotto' => 'Cessione', 'is_oneclient' => 1],
            ['id' => 3, 'name' => 'Cessioni del V pensione', 'code' => 'CessioneCQP', 'oam_code' => 'A.2', 'tipo_prodotto' => 'Cessione', 'is_oneclient' => 1],
            // --- DELEGA ---
            ['id' => 4, 'name' => 'Delegazioni di pagamento', 'code' => 'Delega', 'oam_code' => 'A.2', 'tipo_prodotto' => 'Delega', 'is_oneclient' => 1],
            // --- PRESTITI ---
            ['id' => 12, 'name' => 'Credito personale', 'code' => 'CRED_PERS', 'oam_code' => 'A.10', 'tipo_prodotto' => 'Prestito', 'is_oneclient' => 1],
            ['id' => 13, 'name' => 'Credito finalizzato', 'code' => 'CRED_FIN', 'oam_code' => 'A.11', 'tipo_prodotto' => 'Prestito', 'is_oneclient' => 1],
            ['id' => 29, 'name' => 'Chirografario', 'code' => 'Chirografario', 'oam_code' => 'A.10', 'tipo_prodotto' => 'Prestito', 'is_oneclient' => 1],
            ['id' => 30, 'name' => 'Microcredito', 'code' => 'Microcredito', 'oam_code' => 'A.10', 'tipo_prodotto' => 'Prestito', 'is_oneclient' => 1],
            ['id' => 35, 'name' => 'Prestito', 'code' => 'PREST', 'oam_code' => 'A.10', 'tipo_prodotto' => 'Prestito', 'is_oneclient' => 1],
            // --- TFS ---
            ['id' => 24, 'name' => 'TFS', 'code' => 'TFS', 'oam_code' => 'A.4', 'tipo_prodotto' => 'TFS', 'is_oneclient' => 1],
            // --- AZIENDALE ---
            ['id' => 27, 'name' => 'Aziendale', 'code' => 'Aziendale', 'oam_code' => 'A.1', 'tipo_prodotto' => 'Aziendale', 'is_oneclient' => 1],
            ['id' => 33, 'name' => 'PRESTITO AZIENDALE', 'code' => 'PRESTITO AZIENDALE', 'oam_code' => 'A.1', 'tipo_prodotto' => 'Aziendale', 'is_oneclient' => 1],
            // --- ALTRI (Mappati su categorie logiche o lasciati null se non rientrano) ---
            ['id' => 5, 'name' => 'Factoring crediti', 'code' => 'FACT', 'oam_code' => 'A.1', 'tipo_prodotto' => 'Aziendale', 'is_oneclient' => 1],
            ['id' => 11, 'name' => 'Aperture di credito in conto corrente', 'code' => 'APERT_CCC', 'oam_code' => 'A.1', 'tipo_prodotto' => 'Aziendale', 'is_oneclient' => 1],
            ['id' => 12, 'name' => 'Consulenza', 'code' => 'CONSULENZA', 'oam_code' => '', 'tipo_prodotto' => 'consulenza', 'is_oneclient' => 1],
            ['id' => 13, 'name' => 'Segnalazione mutuo', 'code' => 'MUTUOSEG', 'oam_code' => '', 'tipo_prodotto' => 'Mutux', 'is_oneclient' => 1],
        ];
        foreach ($practices as $practice) {
            DB::table('practice_scopes')->updateOrInsert(
                ['id' => $practice['id']],
                array_merge($practice, [
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('practice Scopes seeded successfully');
    }
}
