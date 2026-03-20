<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\ClientMandate;
use App\Models\Company;
use App\Models\Document;
use App\Models\DocumentScope;
use App\Models\DocumentType;
use App\Models\Practice;
use App\Models\PracticeScope;
use App\Models\Principal;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComplianceSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (!$company)
            return;

        $clientMandate = ClientMandate::first();
        $principal = Principal::first();
        $agent = Agent::first();
        $practiceScope = PracticeScope::first();

        // Skip if essential data is missing
        if (!$clientMandate || !$principal || !$agent || !$practiceScope) {
            return;
        }

        // Create 20 Practices
        for ($i = 1; $i <= 20; $i++) {
            $practice = Practice::create([
                'company_id' => $company->id,
                'client_mandate_id' => $clientMandate->id,
                'principal_id' => $principal->id,
                'agent_id' => $agent->id,
                'name' => "Pratica Test Compliance $i",
                'CRM_code' => 'CRM-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'principal_code' => 'PRIN-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'amount' => 50000 + ($i * 1000),
                'net' => 48000 + ($i * 1000),
                'practice_scope_id' => $practiceScope->id,
                'status' => 'working',
                'perfected_at' => now(),
                'is_active' => 1,
            ]);

            // Add Privacy doc to first 10
            if ($i <= 10) {
                $privacyType = DocumentType::whereHas('scopes', fn($q) => $q->where('document_scopes.name', 'Privacy'))->first();
                if ($privacyType) {
                    Document::create([
                        'id' => \Illuminate\Support\Str::uuid(),
                        'company_id' => $company->id,
                        'practice_id' => $practice->id,
                        'document_type_id' => $privacyType->id,
                        'name' => 'Consenso Privacy Firmato',
                        'status' => 'uploaded',
                    ]);
                }
            }

            // Add AML doc to first 5
            if ($i <= 5) {
                $amlType = DocumentType::whereHas('scopes', fn($q) => $q->where('document_scopes.name', 'AML'))->first();
                if ($amlType) {
                    Document::create([
                        'id' => \Illuminate\Support\Str::uuid(),
                        'company_id' => $company->id,
                        'practice_id' => $practice->id,
                        'document_type_id' => $amlType->id,
                        'name' => 'Modulo AML Compilato',
                        'status' => 'uploaded',
                    ]);
                }
            }
        }
    }
}
