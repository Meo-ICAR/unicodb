<?php

namespace App\Services;

use App\Models\Company;
use App\Models\OamCode;
use App\Models\OamScope;
use App\Models\Practice;
use App\Models\PracticeCommission;
use App\Models\PracticeOam;
use App\Models\PracticeOamBase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class PracticeOamService
{
    /**
     * Sync practice_oams for a company:
     * 1. Delete all existing practice_oam records for the company
     * 2. Insert practices that meet the criteria:
     *    - inserted_at < $endDate AND erogated_at IS NULL
     *    - OR erogated_at BETWEEN $startDate AND $endDate
     */
    public function syncPracticeOamsForCompany(string $companyId, ?string $startDate, ?string $endDate): void
    {
        if (empty($companyId)) {
            $companyId = Company::first()->id;
        }
        if (empty($startDate)) {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
            if (now()->month < 5) {
                $startDate = Carbon::parse($startDate)->subMonths(6)->format('Y-m-d');
            }
        }
        // Always calculate endDate based on the final startDate value
        if (empty($endDate)) {
            $endDate = Carbon::parse($startDate)->addMonths(6)->format('Y-m-d');
        }

        // Convert dates to Carbon objects for comparisons
        $startDateCarbon = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);

        try {
            DB::beginTransaction();

            // Step 1: Delete existing practice_oam records for the date range
            $deletedCount = PracticeOam::where('company_id', $companyId)->delete();

            /*
             * ->where('is_active', true)
             * ->where(function ($query) use ($startDateCarbon, $endDateCarbon) {
             *     // Delete records that would be recreated by this sync
             *     $query
             *         ->where(function ($subQuery) use ($startDateCarbon, $endDateCarbon) {
             *             $subQuery
             *                 ->whereNotNull('erogated_at')
             *                 ->where('erogated_at', '>=', $startDateCarbon)
             *                 ->where('erogated_at', '<=', $endDateCarbon);
             *         })
             *         ->orWhere(function ($subQuery) use ($startDateCarbon, $endDateCarbon) {
             *             $subQuery
             *                 ->whereNull('erogated_at')
             *                 ->where('inserted_at', '>=', $startDateCarbon)
             *                 ->where('inserted_at', '<=', $endDateCarbon);
             *         })
             *         ->orWhere(function ($subQuery) use ($startDateCarbon, $endDateCarbon) {
             *             $subQuery
             *                 ->whereNotNull('invoice_at')
             *                 ->where('invoice_at', '>=', $startDateCarbon)
             *                 ->where('invoice_at', '<=', $endDateCarbon);
             *         });
             * })
             */

            Log::info("Deleted {$deletedCount} practice_oam records for company {$companyId} in date range");

            // Step 2: Get practices that meet the criteria
            $practicesQuery = Practice::where(function ($query) use ($startDateCarbon, $endDateCarbon) {
                // Practices sent before end date AND (perfected after start date OR not perfected yet)
                $query
                    ->where('sended_at', '<', '2026-01-01')
                    ->where(function ($q) use ($startDateCarbon) {
                        $q
                            ->where('erogated_at', '>=', $startDateCarbon)
                            ->orWhereNull('erogated_at');
                    });
            })
                ->orWhere(function ($query) use ($startDateCarbon, $endDateCarbon) {
                    // Practices that received invoices between start and end dates
                    $query
                        ->where('invoice_at', '>=', $startDateCarbon)
                        ->where('erogated_at', '<=', $startDateCarbon);
                })
                ->whereNull('rejected_at')
                ->whereNotNull('brokerage_fee');

            // Debug: Log the SQL query
            Log::info('SQL Query: ' . $practicesQuery->toSql());
            Log::info('Query Bindings: ' . json_encode($practicesQuery->getBindings()));

            $practicesQuery = $practicesQuery->where('tipo_prodotto', '!=', 'Polizza')->where('tipo_prodotto', '!=', 'Utenza')->where('brokerage_fee', '>', 0);
            Log::info('SQL Query: ' . $practicesQuery->toSql());
            Log::info('Query Bindings: ' . json_encode($practicesQuery->getBindings()));

            $practices = $practicesQuery->get();

            Log::info("Found {$practices->count()} practices to process");

            // Step 3: Insert new practice_oam records
            $insertedCount = 0;
            $skippedCount = 0;

            foreach ($practices as $practice) {
                $brokerageFee = $practice->brokerage_fee;
                if ($brokerageFee <= 0 || $brokerageFee === null) {
                    $skippedCount++;
                    continue;
                }
                $tipoProdotto = $practice->tipo_prodotto;
                if (($tipoProdotto == 'Polizza') || ($tipoProdotto == 'Utenza')) {
                    $skippedCount++;
                    continue;
                }

                $erogato = $practice->amount;
                $liquidato = $practice->net;
                $erogato_lavorazione = 0;
                $liquidato_lavorazione = 0;
                $CRMcode = $practice->CRM_code;
                $parcticeName = $practice->name;
                $commissionSums = $this->getPracticeCommissionSums($practice);
                $somma = $commissionSums['somma'];

                if ($somma > 0) {
                    // Assicuriamoci che entrambi siano oggetti Carbon per un confronto granulare
                    $insertedAt = $practice->sended_at;
                    $erogatedAt = $practice->erogated_at;
                    $invoiceAt = $practice->invoice_at;

                    $perfectedAt = $erogatedAt;  // $practice->perfected_at;
                    $is_perfected = $erogatedAt && $erogatedAt >= $insertedAt;
                    $is_perfected = $is_perfected && $erogatedAt < $endDateCarbon;
                    $mese = 0;
                    $isInvoice = $invoiceAt ? 1 : 0;
                    $isBefore = false;
                    if ($is_perfected) {
                        $mese = (int) $erogatedAt->format('n');
                        if ($isInvoice) {
                            $isBefore = $erogatedAt < $startDateCarbon;
                        }
                    }
                    $isAfter = false;
                    if ($isInvoice) {
                        $isAfter = $invoiceAt > $endDate;
                    }

                    $compenso = $commissionSums['compenso'];
                    $premio = $commissionSums['premio'];
                    $assicurazione = $commissionSums['assicurazione'];

                    if (!($tipoProdotto == 'Non so')) {
                        $compenso = $compenso + $premio + $assicurazione;
                        $premio = 0;
                        $assicurazione = 0;
                    }
                    if (!$is_perfected) {
                        $commissionSums['compenso_lavorazione'] = $commissionSums['compenso'];
                        $commissionSums['provvigione_lavorazione'] = $commissionSums['provvigione'];
                        $erogato_lavorazione = $erogato;
                        $liquidato_lavorazione = $liquidato;

                        $commissionSums['compenso'] = 0;
                        $erogato = 0;
                        $liquidato = 0;
                        $commissionSums['provvigione'] = 0;
                    }
                    $oam_code = $practice?->practiceScope?->oam_code;
                    $comCliente = $commissionSums['cliente'] ?? 0;
                    if (!empty($oam_code)) {
                        $oam_name = OamScope::where('code', $oam_code)->first()?->name;

                        $oam_name = $oam_code . ' ' . $oam_name;
                    } else {
                        $oam_name = '--';
                    }
                    if (($tipoProdotto == 'Mutuo') && ($somma == $comCliente)) {
                        $oam_code = $oam_name;
                        //  Log::info("Sync completed for company {$companyId}: {$insertedCount} practice_oam records inserted");
                    }

                    // Use effective perfected date (perfected_at or fallback to erogated_at)

                    PracticeOam::updateOrCreate(
                        ['practice_id' => $practice->id],
                        [
                            'company_id' => $companyId,
                            'oam_code_id' => $practice->practiceScope?->oam_code_id ?? null,
                            'oam_code' => $oam_code,
                            'oam_name' => $oam_name,
                            'principal_name' => $practice->principal->name,
                            'CRM_code' => $practice->CRM_code ?? null,
                            'practice_name' => $practice->name ?? null,
                            //    'type' => $tipoProdotto,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'is_conventioned' => ($compenso > 0) ? 1 : 0,
                            'is_notconventioned' => !($compenso > 0) ? 1 : 0,
                            'is_notconvenctioned' => !($compenso > 0) ? 1 : 0,
                            'is_previous' => 0,  // Default value
                            'mese' => $mese,
                            'tipo_prodotto' => $tipoProdotto,
                            'name' => $practice->principal->name,
                            // Commission sums based on tipo grouping
                            'erogato' => $erogato ?? 0,
                            'erogato_lavorazione' => $erogato_lavorazione ?? 0,
                            'liquidato' => $liquidato ?? 0,
                            'liquidato_lavorazione' => $liquidato_lavorazione ?? 0,
                            'compenso' => $compenso ?? 0,
                            'compenso_lavorazione' => $commissionSums['compenso_lavorazione'] ?? 0,
                            'compenso_premio' => $premio ?? 0,  // premio assicurativo
                            'compenso_rimborso' => $commissionSums['rimborso'] ?? 0,
                            'compenso_assicurazione' => $assicurazione ?? 0,
                            'compenso_cliente' => $comCliente,
                            'storno' => $commissionSums['storno'] ?? 0,
                            'provvigione' => $commissionSums['provvigione'] ?? 0,
                            'provvigione_lavorazione' => $commissionSums['provvigione_lavorazione'] ?? 0,
                            'provvigione_premio' => $commissionSums['premioagente'] ?? 0,
                            'provvigione_rimborso' => $commissionSums['rimborso'] ?? 0,
                            'provvigione_assicurazione' => $commissionSums['provvigione_assicurazione'] ?? null,
                            'provvigione_storno' => $commissionSums['storno'] ?? null,
                            'is_active' => 1,  // Default to active
                            'inserted_at' => $insertedAt ? $insertedAt->format('Y-m-d') : null,
                            'erogated_at' => $erogatedAt ? $erogatedAt->format('Y-m-d') : null,
                            'perfected_at' => $perfectedAt ? $perfectedAt->format('Y-m-d') : null,
                            'is_perfected' => $is_perfected ? 1 : 0,
                            'is_working' => !$is_perfected ? 1 : 0,
                            'invoice_at' => $invoiceAt ? $invoiceAt->format('Y-m-d') : null,
                            'is_invoice' => $isInvoice,
                            'is_before' => $isBefore,
                            'is_after' => $isAfter,
                            'accepted_at' => $practice->approved_at ? $practice->approved_at->format('Y-m-d') : null,
                            //   'approved_at' => $practice->approved_at ? $practice->approved_at->format('Y-m-d') : null,
                            'is_cancel' => $practice->canceled_at ? 1 : 0,
                            'canceled_at' => $practice->canceled_at ? $practice->canceled_at->format('Y-m-d') : null,
                        ]
                    );
                    $insertedCount++;
                } else {
                    Log::info("{$skippedCount} Practice { $CRMcode - $parcticeName} skipped - no commissions found");
                    $skippedCount++;
                }
            }

            DB::commit();

            //   Log::info("Sync completed for company {$companyId}: {$insertedCount} practice_oam records inserted, {$skippedCount} practices skipped");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error syncing practice_oams for company {$companyId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get practice commission sums grouped by tipo
     */
    private function getPracticeCommissionSums(Practice $practice): array
    {
        $commissions = PracticeCommission::where('practice_id', $practice->id)->get();

        // Debug: Check if commissions exist
        $n = $commissions->count();
        if ($n > 0) {
            //  Log::info("Practice {$practice->id} has {$n} commissions");
        }

        // Initialize sums
        $compenso = 0;
        $cliente = 0;
        $provvigione = 0;
        $premio = 0;
        $premioagente = 0;
        $assicurazione = 0;
        $storno = 0;
        $somma = 0;
        $erogato = 0;

        $i = 0;
        // Process each commission
        foreach ($commissions as $commission) {
            $i++;
            //    Log::info('Commission ' . $i . " data for practice {$practice->id}: " . json_encode($commission));
            $amount = $commission->amount ?? 0;
            //  Log::info("Commission data for practice {$practice->id}: " . json_encode($commission));

            $tipo = strtolower($commission->tipo ?? '');
            $name = strtolower($commission->name ?? '');

            // Non-agent, non-client commissions
            if ($tipo === 'istituto') {
                $compenso += $amount;
                //
                // Non-agent premiums
                if (strpos($name, 'premio')) {
                    $premio += $amount;
                } else {
                    if (strpos($name, 'polizza') || strpos($name, 'broker')) {
                        $assicurazione += $amount;
                    } else {
                        //   $compenso += $amount;
                    }
                }
            }

            if ($tipo === 'cliente') {
                $cliente += $amount;
            }

            // Agent commissions
            if ($tipo === 'agente') {
                // Agent premiums
                if (strpos($name, 'premio') !== false) {
                    $premioagente += $amount;
                } else {
                    $provvigione += $amount;
                }
            }

            //
            // Storno
            if ($commission->is_storno) {
                $storno += $amount;
                continue;
            }
        }

        $commissionsa = [
            'compenso' => $compenso - $premio ?: 0,
            'cliente' => $cliente ?: 0,
            'provvigione' => $provvigione - $premioagente ?: 0,
            'premio' => $premio ?: 0,
            'premioagente' => $premioagente ?: 0,
            'storno' => $storno ?: 0,
            'assicurazione' => $assicurazione ?: 0,
            'provvigione_assicurazione' => $assicurazione ?: 0,
            'somma' => $compenso + $provvigione + $premio + $premioagente + $cliente + $storno + $assicurazione,
        ];

        return $commissionsa;
    }

    /**
     * Get statistics about the sync operation
     */
    public function getSyncStats(string $companyId, ?string $startDate = null, ?string $endDate = null): array
    {
        // Use default values if null
        if (empty($startDate)) {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
            if (now()->month < 5) {
                $startDate = Carbon::parse($startDate)->subMonths(6)->format('Y-m-d');
            }
        }
        if (empty($endDate)) {
            $endDate = Carbon::parse($startDate)->addMonths(6)->format('Y-m-d');
        }

        // Convert dates to Carbon objects for comparisons
        $startDateCarbon = Carbon::parse($startDate);
        $endDateCarbon = Carbon::parse($endDate);

        $totalPractices = Practice::where('company_id', $companyId)->count();

        $eligiblePractices = Practice::where('company_id', $companyId)
            ->whereNull('rejected_at')
            ->where(function ($query) use ($startDateCarbon, $endDateCarbon) {
                // Practices sent before end date AND (perfected after start date OR not perfected yet)
                $query
                    ->where('sended_at', '<', $endDateCarbon)
                    ->where(function ($q) use ($startDateCarbon) {
                        $q
                            ->where('erogated_at', '>=', $startDateCarbon)
                            ->orWhereNull('erogated_at');
                    });
            })
            ->orWhere(function ($query) use ($startDateCarbon, $endDateCarbon) {
                // Practices perfected between start and end dates
                $query
                    ->where('invoice_at', '>=', $startDateCarbon)
                    ->where('invoice_at', '<', $endDateCarbon);
            })
            ->count();

        $currentPracticeOams = PracticeOam::where('company_id', $companyId)
            ->where('is_active', true)
            ->count();

        return [
            'total_practices' => (int) $totalPractices,
            'eligible_practices' => (int) $eligiblePractices,
            'current_practice_oams' => (int) $currentPracticeOams,
            'needs_sync' => (int) $currentPracticeOams !== (int) $eligiblePractices,
        ];
    }

    /**
     * Bulk sync for multiple companies
     */
    public function syncPracticeOamsForMultipleCompanies(array $companyIds, string $startDate, string $endDate): array
    {
        $results = [];

        foreach ($companyIds as $companyId) {
            if (empty($companyId)) {
                $results[$companyId] = [
                    'success' => false,
                    'message' => 'Company ID cannot be empty',
                ];
                continue;
            }

            try {
                $this->syncPracticeOamsForCompany($companyId, $startDate, $endDate);
                $stats = $this->getSyncStats($companyId, $startDate, $endDate);

                $results[$companyId] = [
                    'success' => true,
                    'message' => 'Sync completed successfully',
                    'stats' => $stats,
                ];
            } catch (\Exception $e) {
                $results[$companyId] = [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Populate practice_oam_base table for a company
     * This table is used for export purposes and contains aggregated data by OAM
     */
    public function populatePracticeOamBaseForCompany(string $companyId): void
    {
        try {
            DB::beginTransaction();

            // Clear existing data for the company
            PracticeOamBase::where('company_id', $companyId)->delete();

            // Get aggregated data from practice_oams table
            $totals = DB::table('practice_oams')
                ->select([
                    'oam_name as B_OAM',
                    DB::raw('SUM(is_conventioned) as C_Convenzionata'),
                    DB::raw('SUM(is_notconventioned) as D_Non_Convenzionata'),
                    DB::raw('SUM(is_perfected) as E_Intermediate'),
                    DB::raw('SUM(is_working) as F_Lavorazione'),
                    DB::raw('SUM(erogato) as G_Erogato'),
                    DB::raw('SUM(erogato_lavorazione) as H_Erogato_Lavorazione'),
                    DB::raw('SUM(compenso_cliente) as I_Provvigione_Cliente'),
                    DB::raw('SUM(compenso) as J_Provvigione_Istituto'),
                    DB::raw('SUM(compenso_lavorazione) as K_Provvigione_Istituto_Lavorazione'),
                    DB::raw('SUM(provvigione) as O_Provvigione_Rete'),
                ])
                ->where('company_id', $companyId)
                ->where('is_active', true)
                ->groupBy('oam_name')
                ->get();

            // Insert aggregated data into practice_oam_base table
            foreach ($totals as $row) {
                PracticeOamBase::create([
                    'company_id' => $companyId,
                    'B_OAM' => $row->B_OAM,
                    'C_Convenzionata' => $row->C_Convenzionata,
                    'D_Non_Convenzionata' => $row->D_Non_Convenzionata,
                    'E_Intermediate' => $row->E_Intermediate,
                    'F_Lavorazione' => $row->F_Lavorazione,
                    'G_Erogato' => $row->G_Erogato,
                    'H_Erogato_Lavorazione' => $row->H_Erogato_Lavorazione,
                    'I_Provvigione_Cliente' => $row->I_Provvigione_Cliente,
                    'J_Provvigione_Istituto' => $row->J_Provvigione_Istituto,
                    'K_Provvigione_Istituto_Lavorazione' => $row->K_Provvigione_Istituto_Lavorazione,
                    'O_Provvigione_Rete' => $row->O_Provvigione_Rete,
                ]);
            }

            DB::commit();

            //   Log::info("Practice OAM base table populated for company {$companyId}: {$totals->count()} OAM records");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error populating practice_oam_base for company {$companyId}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get practice OAM base statistics for a company
     */
    public function getPracticeOamBaseStats(string $companyId): array
    {
        $stats = PracticeOamBase::where('company_id', $companyId)
            ->select([
                DB::raw('COUNT(*) as total_oams'),
                DB::raw('SUM(C_Convenzionata) as total_convenzionate'),
                DB::raw('SUM(D_Non_Convenzionata) as total_non_convenzionate'),
                DB::raw('SUM(E_Intermediate) as total_intermediate'),
                DB::raw('SUM(F_Lavorazione) as total_lavorazione'),
                DB::raw('SUM(G_Erogato) as total_erogato'),
                DB::raw('SUM(H_Erogato_Lavorazione) as total_erogato_lavorazione'),
                DB::raw('SUM(I_Provvigione_Cliente) as total_provvigione_cliente'),
                DB::raw('SUM(J_Provvigione_Istituto) as total_provvigione_istituto'),
                DB::raw('SUM(K_Provvigione_Istituto_Lavorazione) as total_provvigione_istituto_lavorazione'),
                DB::raw('SUM(O_Provvigione_Rete) as total_provvigione_rete'),
            ])
            ->first();

        return [
            'total_oams' => (int) $stats->total_oams,
            'total_convenzionate' => (int) $stats->total_convenzionate,
            'total_non_convenzionate' => (int) $stats->total_non_convenzionate,
            'total_intermediate' => (int) $stats->total_intermediate,
            'total_lavorazione' => (int) $stats->total_lavorazione,
            'total_erogato' => (float) $stats->total_erogato,
            'total_erogato_lavorazione' => (float) $stats->total_erogato_lavorazione,
            'total_provvigione_cliente' => (float) $stats->total_provvigione_cliente,
            'total_provvigione_istituto' => (float) $stats->total_provvigione_istituto,
            'total_provvigione_istituto_lavorazione' => (float) $stats->total_provvigione_istituto_lavorazione,
            'total_provvigione_rete' => (float) $stats->total_provvigione_rete,
        ];
    }
}
