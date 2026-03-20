<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Principal;
use App\Models\PrincipalEmployee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPrincipalsFromRuiCollaboratori extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rui:sync-principals-from-collaboratori
                            {--company-id= : Process specific company only}
                            {--batch=1000 : Number of records to process in each batch}
                            {--dry-run : Show what would be done without making changes}
                            {--force : Force update even if names match}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync principals from rui_collaboratori based on RUI registration numbers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Syncing principals from RUI collaboratori...');

        $companyId = $this->option('company-id');
        $batchSize = (int) $this->option('batch');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        // Get companies to process - only those with numero_iscrizione_rui
        $companiesQuery = Company::select('id', 'name', 'numero_iscrizione_rui')
            ->whereNotNull('numero_iscrizione_rui')
            ->where('numero_iscrizione_rui', '!=', '');

        if ($companyId) {
            $companiesQuery->where('id', $companyId);
        }
        $companies = $companiesQuery->get();

        foreach ($companies as $company) {
            $this->processCompany($company, $dryRun, $force);
        }

        $this->info('✅ Principal sync completed!');
        return 0;
    }

    private function processCompany($company, bool $dryRun, bool $force)
    {
        $this->line("\n📋 Processing company: {$company->name} ({$company->id})");
        $this->line("  🎯 RUI Number: {$company->numero_iscrizione_rui}");

        // Get all principals for this company that have empty numero_iscrizione_rui

        // Get principal collaboratori for matching
        $principalCollaboratori = $company
            ->ruiCollaboratoriPrincipal()
            ->select('num_iscr_collaboratori_i_liv', 'intermediario', 'num_iscr_intermediario')
            ->where('livello', '=', 'I')
            ->distinct()
            ->get();

        if ($principalCollaboratori->isEmpty()) {
            $this->line('  ℹ️  No principal collaboratori found for matching');
            return;
        }

        $this->line('  📊 Found ' . $principalCollaboratori->count() . ' principal collaboratori');

        $processedCount = 0;
        $updatedCount = 0;

        foreach ($principalCollaboratori as $principalCollaboratore) {
            $result = $this->processPrincipal($principalCollaboratore, $dryRun, $force);

            if ($result['processed']) {
                $processedCount++;
                if ($result['updated'])
                    $updatedCount++;
            }
        }

        // Process employees
        $employeeCollaboratori = $company
            ->ruiCollaboratoriEmployee()
            ->select('num_iscr_collaboratori_ii_liv', 'dipendente', 'num_iscr_intermediario')
            ->where('livello', '=', 'II')
            ->distinct()
            ->get();

        if (!$employeeCollaboratori->isEmpty()) {
            $this->line('  📊 Found ' . $employeeCollaboratori->count() . ' employee collaboratori');

            foreach ($employeeCollaboratori as $employeeCollaboratore) {
                $dipendenteName = $employeeCollaboratore->dipendente ?? '';
                $this->line("  🔍 Processing employee / agent collaboratore: '{$dipendenteName}' -> RUI: " . ($employeeCollaboratore->num_iscr_collaboratori_ii_liv ?? 'NULL'));

                $result = $this->processEmployee($employeeCollaboratore, $company, $dryRun, $force);

                if ($result['processed']) {
                    $processedCount++;
                    if ($result['updated'])
                        $updatedCount++;
                }
            }
        }
        // Aggiungi relazioni principal collaboratori o agent
        if (!$employeeCollaboratori->isEmpty()) {
            $this->line('  📊 Found ' . $employeeCollaboratori->count() . ' principal  collaboratori');

            foreach ($employeeCollaboratori as $employeeCollaboratore) {
                if (empty($employeeCollaboratore->num_iscr_collaboratori_ii_liv)) {
                    continue;
                }
                $ruiIntermediario = $employeeCollaboratore->num_iscr_intermediario ?? '';
                $ruiNumber = $employeeCollaboratore->num_iscr_collaboratori_ii_liv ?? '';
                $dipendenteName = $employeeCollaboratore->dipendente ?? '';
                $principal = Principal::where('company_id', $company->id)
                    ->where('numero_iscrizione_rui', '=', $ruiIntermediario)
                    ->first();

                $this->line("  🔍 Looking for principal with RUI: {$ruiIntermediario} -> Found: " . ($principal ? $principal->name : 'NULL'));

                if (!empty($principal)) {
                    $employee = Employee::where('company_id', $company->id)
                        ->where('numero_iscrizione_rui', '=', $ruiNumber)
                        ->first();

                    $this->line("  🔍 Looking for employee with RUI: {$ruiNumber} -> Found: " . ($employee ? $employee->name : 'NULL'));

                    if ($employee) {
                        if (!$dryRun) {
                            PrincipalEmployee::updateOrCreate(
                                ['principal_id' => $principal->id, 'employee_id' => $employee->id],
                                [
                                    'num_iscr_intermediario' => $employeeCollaboratore->num_iscr_intermediario,
                                    'num_iscr_collaboratori_ii_liv' => $employeeCollaboratore->num_iscr_collaboratori_ii_liv,
                                    'start_date' => now(),
                                    'is_active' => true,
                                    'updated_at' => now(),
                                ]
                            );
                        }
                        $this->line("  🔍 Processing employee collaboratore: '{$dipendenteName}' -> RUI: {$ruiNumber} -> Principal ID: {$principal->id}");
                    } else {
                        $agent = Agent::where('company_id', $company->id)
                            ->where('numero_iscrizione_rui', '=', $ruiNumber)
                            ->first();

                        $this->line("  🔍 Looking for agent with RUI: {$ruiNumber} -> Found: " . ($agent ? $agent->name : 'NULL'));

                        if ($agent) {
                            if (!$dryRun) {
                                PrincipalEmployee::updateOrCreate(
                                    ['principal_id' => $principal->id, 'agent_id' => $agent->id],
                                    [
                                        'num_iscr_intermediario' => $employeeCollaboratore->num_iscr_intermediario,
                                        'num_iscr_collaboratori_ii_liv' => $employeeCollaboratore->num_iscr_collaboratori_ii_liv,
                                        'start_date' => now(),
                                        'is_active' => true,
                                        'updated_at' => now(),
                                    ]
                                );
                            }
                            $this->line("  🔍 Processing agent collaboratore: '{$dipendenteName}' -> RUI: {$ruiNumber} -> Principal ID: {$principal->id}");
                        }
                    }
                }
            }
        }
        $this->line("  📊 Results: {$processedCount} processed, {$updatedCount} updated");
    }

    private function processPrincipal($principalCollaboratore, bool $dryRun, bool $force): array
    {
        $intermediarioName = $principalCollaboratore->intermediario ?? '';
        $ruiNumber = $principalCollaboratore->num_iscr_intermediario ?? '';

        if (empty($ruiNumber) || empty($intermediarioName)) {
            return ['processed' => false, 'updated' => false];
        }

        // Find principal to update by checking if intermediario contains principal name
        $principal = Principal::where(function ($query) use ($intermediarioName) {
            $query
                ->whereNull('numero_iscrizione_rui')
                ->orWhere('numero_iscrizione_rui', '');
        })
            ->where(function ($query) use ($intermediarioName) {
                $query
                    ->whereRaw("? LIKE CONCAT('%', name, '%')", [$intermediarioName])
                    ->orWhereRaw("LOWER(?) LIKE CONCAT('%', LOWER(name), '%')", [strtolower($intermediarioName)]);
            })
            ->first();

        if ($principal) {
            if (!$dryRun) {
                $principal->update([
                    'oam_name' => $intermediarioName,
                    'numero_iscrizione_rui' => $ruiNumber,
                    'updated_at' => now(),
                ]);
            }
        }
        if (!empty($principal) && empty($principal->oam_at)) {
            if (!$dryRun) {
                $principal->update([
                    'oam_at' => $principal->rui->data_iscrizione,
                    'oam_name' => $principal->rui->cognome_nome,
                    'updated_at' => now(),
                ]);
            }
            $this->line("    🔄 Updated employee AT '{$dipendenteName}' -> RUI: {$ruiNumber}");
        }

        $this->line("    🔄 Update principal '{$intermediarioName}' -> OAM: '{$intermediarioName}', RUI: {$ruiNumber}");
        return ['processed' => true, 'updated' => true];
    }

    private function processEmployee($employeeCollaboratore, $company, bool $dryRun, bool $force): array
    {
        $ruiNumber = $employeeCollaboratore->num_iscr_collaboratori_ii_liv ?? '';
        $dipendenteName = $employeeCollaboratore->dipendente ?? '';
        $isQualificaResponsabile = $employeeCollaboratore->qualifica_rapporto == 'Responsabile di società';

        if (empty($ruiNumber) || empty($dipendenteName)) {
            return ['processed' => false, 'updated' => false];
        }

        // Find employee to update by checking if employee name is contained in dipendente
        $employee = Employee::where(function ($query) use ($dipendenteName) {
            $query
                ->whereNull('numero_iscrizione_rui')
                ->orWhere('numero_iscrizione_rui', '');
        })
            ->where(function ($query) use ($dipendenteName) {
                $query
                    ->whereRaw("? LIKE CONCAT('%', name, '%')", [strtolower($dipendenteName)])
                    ->orWhereRaw("LOWER(?) LIKE CONCAT('%', LOWER(name), '%')", [strtolower($dipendenteName)]);
            })
            ->first();
        $type = null;
        if (!$employee) {
            if ($isQualificaResponsabile) {
                $type = 'amministratore';
            } else {
                $type = 'dipendente';  // default value
            }
            if (!$dryRun) {
                $this->line("    🔄 Inserted administrator '{$dipendenteName}' -> RUI: {$ruiNumber}");
                if (!empty($type)) {
                    $employee = Employee::create([
                        'company_id' => $company->id,
                        'name' => $dipendenteName,
                        'employee_types' => $type,
                        'numero_iscrizione_rui' => $ruiNumber,
                        'oam_name' => $dipendenteName,
                        'updated_at' => now(),
                    ]);
                    $this->line("    🔄 Inserted employee '{$dipendenteName}' -> RUI: {$ruiNumber}");
                }
            }
        }
        if ($employee && empty($employee->numero_iscrizione_rui)) {
            if (!$dryRun) {
                $employee->update([
                    'numero_iscrizione_rui' => $ruiNumber,
                    'oam_name' => $dipendenteName,
                    'oam_at' => $employee->rui->data_iscrizione,
                    'updated_at' => now(),
                ]);
                $this->line("    🔄 UPDATED employee '{$dipendenteName}' -> RUI: {$ruiNumber}");
            } else {
                $this->line("    🔄 DRY RUN: Update employee '{$dipendenteName}' -> RUI: {$ruiNumber}");
            }
        } else {
            if (!$isQualificaResponsabile) {
                return $this->processAgent($employeeCollaboratore, $company, $dryRun, $force);
            }
        }
        if (!empty($employee) && empty($employee->oam_at)) {
            if (!$dryRun) {
                $employee->update([
                    'oam_at' => $employee->rui->data_iscrizione,
                    'updated_at' => now(),
                ]);
            }
            $this->line("    🔄 Updated employee AT '{$dipendenteName}' -> RUI: {$ruiNumber}");
        }
        return ['processed' => true, 'updated' => true];
    }

    private function processAgent($agentCollaboratore, $company, bool $dryRun, bool $force): array
    {
        $this->line("  🔍 Processing Agent collaboratore: '{$agentCollaboratore->dipendente}' -> RUI: " . ($agentCollaboratore->num_iscr_collaboratori_ii_liv ?? 'NULL'));
        $dipendenteName = $agentCollaboratore->dipendente ?? '';
        $ruiNumber = $agentCollaboratore->num_iscr_collaboratori_ii_liv ?? '';

        if (empty($ruiNumber) || empty($dipendenteName)) {
            return ['processed' => false, 'updated' => false];
        }

        // Find employee to update by checking if employee name is contained in dipendente
        $agent = Agent::where(function ($query) use ($dipendenteName) {
            $query
                ->whereNull('numero_iscrizione_rui')
                ->orWhere('numero_iscrizione_rui', '');
        })
            ->where(function ($query) use ($dipendenteName) {
                $query
                    ->whereRaw("? LIKE CONCAT('%', name, '%')", [strtolower($dipendenteName)])
                    ->orWhereRaw("LOWER(?) LIKE CONCAT('%', LOWER(name), '%')", [strtolower($dipendenteName)]);
            })
            ->first();
        if (!$agent) {
            Agent::insert([
                'name' => $dipendenteName,
                'numero_iscrizione_rui' => $ruiNumber,
                'oam_name' => $dipendenteName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->line('  🔍 Agent not found, creating new one');
            return ['processed' => false, 'updated' => false];
        }
        if ($agent) {
            if (!$dryRun) {
                $agent->update([
                    'numero_iscrizione_rui' => $ruiNumber,
                    'oam_name' => $dipendenteName,
                    'updated_at' => now(),
                ]);
                $this->line("    🔄 UPDATED agent '{$dipendenteName}' -> RUI: {$ruiNumber}");
            } else {
                $this->line("    🔄 DRY RUN: Update agent '{$dipendenteName}' -> RUI: {$ruiNumber}");
            }
        } else {
            $this->line('  🔍 Agent not found, skipping');
            if (!$dryRun) {
                $agent = Agent::create([
                    'name' => $dipendenteName,
                    'numero_iscrizione_rui' => $ruiNumber,
                    'oam_name' => $dipendenteName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->line("    ➕ Created agent '{$dipendenteName}' -> RUI: {$ruiNumber}");
            }
            return ['processed' => false, 'updated' => false];
        }
        if (empty($agent->oam_at)) {
            $agent->update([
                'oam_at' => $agent->rui->data_iscriziones,
                'updated_at' => now(),
            ]);
            $this->line("    🔄 Updated agent '{$dipendenteName}' -> RUI: {$ruiNumber}");
        }
        if (!empty($agent) && empty($agent->oam_at)) {
            if (!$dryRun) {
                $agent->update([
                    'oam_at' => $agent->rui->data_iscrizione,
                    'updated_at' => now(),
                ]);
            }
            $this->line("    🔄 Updated employee AT '{$dipendenteName}' -> RUI: {$ruiNumber}");
        }
        return ['processed' => true, 'updated' => true];
    }
}
