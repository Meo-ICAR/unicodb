<?php

namespace Database\Seeders;

use App\Models\Audit;
use App\Models\AuditItem;
use App\Models\Company;
use App\Models\ComplianceViolation;
use App\Models\Practice;
use App\Models\Proforma;
use App\Models\ProformaStatus;
use App\Models\TrainingSession;
use App\Models\TrainingTemplate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SupportSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $agentUser = User::where('email', 'agent@agency.com')->first();
        $practice = Practice::first();
        $admin = User::first();
        $user = User::first();

        if (!$company || !$agentUser || !$practice || !$admin) {
            return;
        }

        $now = Carbon::now();

        $violations = [
            [
                'company_id' => $company->id,  // Violazione globale
                'user_id' => null,
                'violatable_type' => null,
                'violatable_id' => null,
                'violation_type' => 'accesso_non_autorizzato',
                'severity' => 'alto',
                'description' => 'Tentativo di accesso a dati clienti da parte di utente non autorizzato. IP rilevato da rete esterna.',
                'affected_subjects_count' => 15,
                'likely_consequences' => 'Possibile esposizione dati personali e finanziari dei clienti coinvolti.',
                'discovery_date' => $now->subDays(2),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'is_dpa_notified' => false,
                'dpa_notified_at' => null,
                'dpa_not_notified_reason' => 'Rischio valutato come improbabile per i diritti degli interessati',
                'are_subjects_notified' => false,
                'resolved_at' => null,
                'resolved_by' => null,
                'resolution_notes' => null,
                'created_at' => $now->subDays(2),
                'updated_at' => $now->subDays(2),
            ],
            [
                'company_id' => $company->id,  // Violazione globale
                'user_id' => $user->id,
                'violatable_type' => 'App\Models\Client',
                'violatable_id' => 123,
                'violation_type' => 'kyc_scaduto',
                'severity' => 'medio',
                'description' => 'Documentazione KYC scaduta per cliente. Manca aggiornamento documenti identità.',
                'affected_subjects_count' => 1,
                'likely_consequences' => 'Impossibilità di proseguire con istruttoria finanziaria fino ad aggiornamento.',
                'discovery_date' => $now->subHours(6),
                'ip_address' => '10.0.0.15',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
                'is_dpa_notified' => false,
                'dpa_notified_at' => null,
                'dpa_not_notified_reason' => null,
                'are_subjects_notified' => true,
                'resolved_at' => $now->subHours(2),
                'resolved_by' => 1,
                'resolution_notes' => 'Cliente contattato e documentazione in via di aggiornamento.',
                'created_at' => $now->subHours(6),
                'updated_at' => $now->subHours(2),
            ],
            [
                'company_id' => $company->id,  // Violazione globale
                'user_id' => null,
                'violatable_type' => null,
                'violatable_id' => null,
                'violation_type' => 'data_breach',
                'severity' => 'critico',
                'description' => 'Potenziale data breach rilevato da sistema di monitoraggio. Traffico anomalo su database clienti.',
                'affected_subjects_count' => 250,
                'likely_consequences' => 'Esposizione massiva dati personali con rischio di furto identità e frodi.',
                'discovery_date' => $now->subMinutes(30),
                'ip_address' => '185.220.101.182',
                'user_agent' => 'curl/7.68.0',
                'is_dpa_notified' => true,
                'dpa_notified_at' => $now->subMinutes(15),
                'dpa_not_notified_reason' => null,
                'are_subjects_notified' => false,
                'resolved_at' => null,
                'resolved_by' => null,
                'resolution_notes' => 'Indagine in corso. Sistema isolato e analisi forense avviata.',
                'created_at' => $now->subMinutes(30),
                'updated_at' => $now->subMinutes(30),
            ],
            [
                'company_id' => $company->id,  // Violazione globale
                'user_id' => $user->id,
                'violatable_type' => 'App\Models\Practice',
                'violatable_id' => 456,
                'violation_type' => 'forzatura_stato',
                'severity' => 'basso',
                'description' => 'Tentativo di modifica stato pratica da non autorizzato a completato.',
                'affected_subjects_count' => 0,
                'likely_consequences' => null,
                'discovery_date' => $now->subDay(),
                'ip_address' => '172.16.0.25',
                'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
                'is_dpa_notified' => false,
                'dpa_notified_at' => null,
                'dpa_not_notified_reason' => null,
                'are_subjects_notified' => false,
                'resolved_at' => $now->subHours(12),
                'resolved_by' => 2,
                'resolution_notes' => "Accesso revocato all'utente. Pratica ripristinata stato corretto.",
                'created_at' => $now->subDay(),
                'updated_at' => $now->subHours(12),
            ],
        ];

        foreach ($violations as $violation) {
            \App\Models\ComplianceViolation::firstOrCreate(
                [
                    'company_id' => $violation['company_id'],
                    'violation_type' => $violation['violation_type']
                ],
                $violation
            );
        }

        // Proformas
        $status = ProformaStatus::first();
        $proforma = Proforma::firstOrCreate(
            ['company_id' => $company->id, 'agent_id' => $agentUser->id, 'month' => date('n'), 'year' => date('Y')],
            [
                'name' => 'Proforma ' . date('n/Y') . ' - Agente',
                'total_commissions' => 1250.5,
                'status' => $status ? $status->name : 'INSERITO',
            ]
        );

        // Practice Commission
        \App\Models\PracticeCommission::firstOrCreate(
            ['practice_id' => $practice->id, 'company_id' => $company->id],
            [
                'proforma_id' => $proforma->id,
                'agent_id' => $agentUser->id,
                'amount' => 1250.5,
                'description' => 'Provvigione Mese Corrente'
            ]
        );

        // Audits
        $audit = Audit::firstOrCreate(
            ['title' => 'Audit Annuale Trasparenza 2025', 'company_id' => $company->id],
            [
                'requester_type' => 'OAM',
                'start_date' => '2025-01-01',
                'status' => 'COMPLETATO',
                'overall_score' => 'Conforme'
            ]
        );

        AuditItem::firstOrCreate(
            ['audit_id' => $audit->id, 'auditable_type' => get_class($practice), 'auditable_id' => $practice->id],
            [
                'name' => 'Verifica Privacy Cliente',
                'result' => 'OK',
            ]
        );

        // Training
        $template = TrainingTemplate::firstOrCreate(
            ['name' => 'Aggiornamento OAM Base'],
            [
                'category' => 'OAM',
                'base_hours' => 30.0,
                'is_mandatory' => 1
            ]
        );

        $session = TrainingSession::firstOrCreate(
            ['training_template_id' => $template->id, 'company_id' => $company->id],
            [
                'name' => 'Sessione Autunnale OAM',
                'total_hours' => 30.0,
                'start_date' => '2024-09-01',
                'end_date' => '2024-09-30'
            ]
        );

        \App\Models\TrainingRecord::firstOrCreate(
            ['training_session_id' => $session->id, 'trainable_type' => 'App\Models\Agent', 'trainable_id' => 1],
            [
                'status' => 'COMPLETATO',
                'hours_attended' => 30.0,
                'score' => 'Idoneo',
                'completion_date' => '2024-10-01'
            ]
        );

        // API Configuration
        $software = \App\Models\SoftwareApplication::first();
        if ($software) {
            \App\Models\ApiConfiguration::firstOrCreate(
                ['software_application_id' => $software->id, 'company_id' => $company->id],
                [
                    'name' => 'Collega CRM Master',
                    'auth_type' => 'API_KEY',
                    'api_key' => 'scj4x8c39nxk21',
                    'api_secret' => 'super_secret',
                    'is_active' => 1
                ]
            );
        }
    }
}
