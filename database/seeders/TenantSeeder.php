<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Employee;
use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Tenant (Company)
        $companiesData = [
            [
                'name' => 'Races Finance S.r.l.',
                'vat_number' => '05822361007',
                'vat_name' => 'Races Finance',
                'oam' => 'A3026',  // Iscritta come Agente in Attività Finanziaria
                'oam_at' => '2012-11-26',
                'oam_name' => 'RACES FINANCE SRL'
            ],

            /*
             * [
             *     'name' => 'Credifacile S.r.l.',
             *     'vat_number' => '02450210419',
             *     'vat_name' => 'Credifacile',
             *     'oam' => 'M168',  // Iscritta come Mediatore Creditizio
             *     'oam_at' => '2013-04-12',
             *     'oam_name' => 'CREDIFACILE S.R.L.'
             * ]
             */
        ];

        foreach ($companiesData as $data) {
            Company::firstOrCreate(
                ['vat_number' => $data['vat_number']],
                [
                    'id' => (string) Str::uuid(),
                    'name' => $data['name'],
                    'vat_name' => $data['vat_name'],
                    'oam' => $data['oam'],
                    'oam_at' => $data['oam_at'],
                    'oam_name' => $data['oam_name']
                ]
            );
        }

        // Recuperiamo le istanze delle Company create
        $races = Company::where('vat_number', '05822361007')->first();
        $credifacile = Company::where('vat_number', '02450210419')->first();
        $company = $races;
        // --- Sedi per Races Finance ---
        if ($races) {
            CompanyBranch::firstOrCreate(
                ['company_id' => $races->id, 'name' => 'Sede Legale Roma'],
                [
                    'is_main_office' => 1,
                    'manager_first_name' => 'Sergio',  // Dato simulato per il manager
                    'manager_last_name' => 'Bracale'
                ]
            );
        }
        $branch = CompanyBranch::where('company_id', $races->id)->first();

        // --- Sedi per Credifacile ---
        if ($credifacile) {
            CompanyBranch::firstOrCreate(
                ['company_id' => $credifacile->id, 'name' => 'Sede Legale Pesaro'],
                [
                    'is_main_office' => 1,
                    'manager_first_name' => 'Luca',  // Dato simulato per il manager
                    'manager_last_name' => 'Giovanelli'
                ]
            );
        }

        // --- Siti per Races Finance ---
        if ($races) {
            Website::firstOrCreate(
                ['domain' => 'www.races.it'],
                [
                    'company_id' => $races->id,
                    'name' => 'Sito Istituzionale Races',
                    'type' => 'Vetrina',
                    'websiteable_type' => 'App\Models\Company',
                    'websiteable_id' => $races->id,
                    'is_active' => 1
                ]
            );
        }

        // --- Siti per Credifacile ---
        if ($credifacile) {
            Website::firstOrCreate(
                ['domain' => 'www.credifacile.it'],
                [
                    'company_id' => $credifacile->id,
                    'name' => 'Portale Credifacile',
                    'type' => 'Vetrina',
                    'websiteable_type' => 'App\Models\Company',
                    'websiteable_id' => $credifacile->id,
                    'is_active' => 1
                ]
            );
        }

        // 4. Super Admin (Global)
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'company_id' => null
            ]
        );

        // 5. Tenant Users (Agents & Admins)
        $tenantAdmin = User::firstOrCreate(
            ['email' => 'user@agency.com'],
            [
                'name' => 'Tenant Manager',
                'password' => Hash::make('password'),
                'company_id' => $company->id
            ]
        );

        $agentUser = User::firstOrCreate(
            ['email' => 'agent@agency.com'],
            [
                'name' => 'Luigi Agente',
                'password' => Hash::make('password'),
                'company_id' => $company->id
            ]
        );

        // 6. Employees (Internal Staff)
        Employee::firstOrCreate(
            ['user_id' => $tenantAdmin->id],
            [
                'company_id' => $company->id,
                'name' => 'Tenant Manager',
                'role_title' => 'Amministratore',
                'email' => 'user@agency.com',
                'company_branch_id' => $branch->id,
            ]
        );
    }
}
