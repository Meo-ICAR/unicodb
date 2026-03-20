<?php

namespace Database\Seeders;

use App\Models\BusinessFunction;
use App\Models\ProcessTask;
use App\Models\RaciAssignment;
use Illuminate\Database\Seeder;

class RaciAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // Definiamo le regole di assegnazione per blocchi di task
        // Struttura: 'slug_parziale' => [ 'FUNCTION_CODE' => 'ROLE' ]
        $assignments = [
            // --- LOGICA CQS ---
            'CessioneCQS-raccolta-documenti' => [
                'BUS-RETE-EXT' => 'R',  // Agente raccoglie
                'BUS-BO' => 'I',  // Backoffice informato
                'CTRL-AML' => 'A',  // AML risponde della regolarità
            ],
            'CessioneCQS-richiesta-certificato' => [
                'BUS-BO' => 'R',  // Backoffice richiede
                'BUS-BO' => 'A',
                'BUS-RETE-EXT' => 'I',
            ],
            'CessioneCQS-notifica-atto' => [
                'BUS-BO' => 'R',
                'GOV-CDA' => 'A',  // La direzione risponde della corretta notifica
                'CTRL-COMPL' => 'C',  // Compliance consultata per PEC/Legale
            ],
            // --- LOGICA MUTUI ---
            'MUT_IPOTECARIO-analisi-preliminare' => [
                'BUS-RETE-EXT' => 'R',
                'BUS-DIRCOM' => 'A',
                'CTRL-RISK' => 'C',
            ],
            'MUT_IPOTECARIO-perizia-immobile' => [
                'BUS-BO' => 'R',
                'BUS-BO' => 'A',
                'BUS-RETE-EXT' => 'I',
            ],
            // --- LOGICA AZIENDALE ---
            'Aziendale-analisi-centrale-rischi' => [
                'CTRL-RISK' => 'R',  // Risk manager analizza CR
                'CTRL-RISK' => 'A',
                'BUS-DIRCOM' => 'C',
            ],
            'Aziendale-richiesta-garanzia-mcc' => [
                'BUS-BO' => 'R',
                'CTRL-COMPL' => 'A',  // Compliance risponde delle dichiarazioni MCC
            ],
            // --- LOGICA TRASVERSALE (Controllo) ---
            'liquidazione' => [
                'SUP-AMM' => 'R',  // Amministrazione paga
                'GOV-CDA' => 'A',  // CdA autorizza
                'BUS-DIRCOM' => 'I',
            ]
        ];

        foreach ($assignments as $taskSlug => $roles) {
            // Cerchiamo i task che contengono o corrispondono allo slug
            $tasks = ProcessTask::where('slug', 'like', "%{$taskSlug}%")->get();

            foreach ($tasks as $task) {
                foreach ($roles as $funcCode => $role) {
                    $function = BusinessFunction::where('code', $funcCode)->first();

                    if ($function) {
                        RaciAssignment::updateOrCreate(
                            [
                                'process_task_id' => $task->id,
                                'business_function_id' => $function->id,
                            ],
                            ['role' => $role]
                        );
                    }
                }
            }
        }
    }
}
