<?php

namespace App\Console\Commands;

use App\Models\AgentCommissionGroup;
use App\Models\PracticeCommission;
use App\Models\PurchaseInvoice;
use Illuminate\Console\Command;

class DetailedCommissionAnalysisCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:detailed-analysis {--agent= : Filter by specific agent ID} {--date-from= : Start date (YYYY-MM-DD)} {--date-to= : End date (YYYY-MM-DD)} {--min-pct= : Minimum commission percentage} {--max-pct= : Maximum commission percentage} {--unmatched : Show only unmatched groups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detailed analysis of commission groups with advanced filtering';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $agentId = $this->option('agent');
        $dateFrom = $this->option('date-from');
        $dateTo = $this->option('date-to');
        $minPct = $this->option('min-pct');
        $maxPct = $this->option('max-pct');
        $unmatchedOnly = $this->option('unmatched');

        $this->info('Detailed Commission Analysis');
        $this->info('============================');

        if ($agentId)
            $this->line("Agent ID: {$agentId}");
        if ($dateFrom)
            $this->line("Date from: {$dateFrom}");
        if ($dateTo)
            $this->line("Date to: {$dateTo}");
        if ($minPct)
            $this->line("Min percentage: {$minPct}%");
        if ($maxPct)
            $this->line("Max percentage: {$maxPct}%");
        if ($unmatchedOnly)
            $this->line('Showing only unmatched groups');

        try {
            $query = AgentCommissionGroup::with(['purchaseInvoice']);

            // Apply filters
            if ($agentId) {
                $query->where('agent_id', $agentId);
            }

            if ($dateFrom) {
                $query->whereDate('invoice_at', '>=', $dateFrom);
            }

            if ($dateTo) {
                $query->whereDate('invoice_at', '<=', $dateTo);
            }

            if ($minPct) {
                $query->where('commission_percentage', '>=', $minPct);
            }

            if ($maxPct) {
                $query->where('commission_percentage', '<=', $maxPct);
            }

            if ($unmatchedOnly) {
                $query->unmatched();
            }

            $groups = $query->orderBy('invoice_at', 'desc')->get();

            $this->displayDetailedResults($groups);

            return 0;
        } catch (\Exception $e) {
            $this->error('Analysis failed: ' . $e->getMessage());
            return 1;
        }
    }

    protected function displayDetailedResults($groups)
    {
        $this->newLine();
        $this->line("Found {$groups->count()} groups matching criteria");

        if ($groups->isEmpty()) {
            return;
        }

        // Summary statistics
        $totalCommission = $groups->sum('total_commission_amount');
        $totalInvoice = $groups->sum('total_invoice_amount');
        $avgPercentage = $groups->avg('commission_percentage');
        $matchedCount = $groups->where('is_matched', true)->count();
        $unmatchedCount = $groups->where('is_matched', false)->count();

        $this->newLine();
        $this->info('Summary Statistics:');
        $this->line('Total Commission: €' . number_format($totalCommission, 2));
        $this->line('Total Invoice: €' . number_format($totalInvoice, 2));
        $this->line('Average Percentage: ' . number_format($avgPercentage, 2) . '%');
        $this->line("Matched: {$matchedCount}");
        $this->line("Unmatched: {$unmatchedCount}");

        // Detailed table
        $this->newLine();
        $this->info('Detailed Results:');

        $headers = ['Date', 'Agent ID', 'Commission', 'Invoice', '%', 'Match', 'Invoice #'];
        $rows = [];

        foreach ($groups as $group) {
            $rows[] = [
                $group->invoice_at->format('Y-m-d'),
                $group->agent_id,
                '€' . number_format($group->total_commission_amount, 2),
                '€' . number_format($group->total_invoice_amount ?? 0, 2),
                number_format($group->commission_percentage ?? 0, 1) . '%',
                $group->is_matched ? 'YES' : 'NO',
                $group->purchaseInvoice->number ?? 'N/A'
            ];
        }

        $this->table($headers, $rows);

        // Show commission details for a specific group if requested
        if ($this->confirm('Show commission details for a specific group?')) {
            $this->showCommissionDetails($groups);
        }
    }

    protected function showCommissionDetails($groups)
    {
        $date = $this->ask('Enter invoice date (YYYY-MM-DD) to see commission details');
        $agentId = $this->ask('Enter agent ID');

        $group = $groups->first(function ($g) use ($date, $agentId) {
            return $g->invoice_at->format('Y-m-d') === $date && $g->agent_id == $agentId;
        });

        if (!$group) {
            $this->warn('No group found with specified criteria');
            return;
        }

        $this->newLine();
        $this->info("Commission Details for Agent {$group->agent_id} - {$group->invoice_at->format('Y-m-d')}");

        $commissions = PracticeCommission::where('tipo', 'Agente')
            ->where('agent_id', $group->agent_id)
            ->whereDate('invoice_at', $group->invoice_at)
            ->get();

        if ($commissions->isEmpty()) {
            $this->warn('No individual commissions found');
            return;
        }

        $headers = ['ID', 'Amount', 'Description', 'Status'];
        $rows = [];

        foreach ($commissions as $comm) {
            $rows[] = [
                $comm->id,
                '€' . number_format($comm->amount, 2),
                $comm->description ?? 'N/A',
                $comm->status_payment ?? 'N/A'
            ];
        }

        $this->table($headers, $rows);

        $this->newLine();
        $this->line('Total individual commissions: ' . $commissions->sum('amount'));
        $this->line('Group total commission: ' . $group->total_commission_amount);
        $this->line('Difference: ' . ($commissions->sum('amount') - $group->total_commission_amount));
    }
}
