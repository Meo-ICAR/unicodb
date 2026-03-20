<?php
namespace Database\Seeders;

use App\Models\BusinessFunction;
use App\Models\Company;
use App\Models\CompanyFunction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyFunctionSeeder extends Seeder
{
    public function run()
    {
        // Disabilito i constraint per velocità
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Pulisco la tabella company_functions per evitare duplicati
        DB::table('company_functions')->truncate();

        // Riabilito i constraint
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Recupero tutte le companies
        $companies = Company::all();

        // 2. Recupero tutti i business functions
        $businessFunctions = BusinessFunction::all();

        // 3. Per ogni company, creo tutti i business functions
        foreach ($companies as $company) {
            foreach ($businessFunctions as $businessFunction) {
                // Creo la company function con valori di default
                CompanyFunction::create([
                    'company_id' => $company->id,
                    'business_function_id' => $businessFunction->id,
                    'employee_id' => null,  // Sarà assegnato dopo
                    'client_id' => null,  // Sarà assegnato dopo
                    'is_privacy' => $businessFunction->is_privacy ?? false,
                    'is_outsourced' => false,  // Default: non esternalizzato
                    'report_frequency' => 'Mensile',  // Default generico
                    'contract_expiry_date' => null,
                    'notes' => "Funzione {$businessFunction->name} assegnata automaticamente a {$company->name}",
                ]);
            }
        }

        $this->command->info('Company functions created for all companies:');
        $this->command->info("- Companies: {$companies->count()}");
        $this->command->info("- Business Functions: {$businessFunctions->count()}");
        $this->command->info('- Total records: ' . ($companies->count() * $businessFunctions->count()));
    }
}
