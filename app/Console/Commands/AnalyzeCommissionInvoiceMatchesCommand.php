<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\AgentCommissionGroup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AnalyzeCommissionInvoiceMatchesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:analyze-matches {--company= : Company ID to filter} {--export : Export to CSV}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze commission-invoice matches and show comparisons';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companyId = $this->option('company');
        $export = $this->option('export');

        $this->info('Analyzing commission-invoice matches...');
        $this->info('Company ID: ' . ($companyId ?: 'All'));

        try {
            $analysis = $this->performAnalysis($companyId);

            $this->displayAnalysis($analysis);

            if ($export) {
                $this->exportToCsv($analysis);
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('Analysis failed: ' . $e->getMessage());
            return 1;
        }
    }

    protected function performAnalysis($companyId)
    {
        $query = AgentCommissionGroup::with(['purchaseInvoice'])
            ->matched();

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $matchedGroups = $query->get();

        $analysis = [
            'total_matched' => $matchedGroups->count(),
            'total_commission_amount' => $matchedGroups->sum('total_commission_amount'),
            'total_invoice_amount' => $matchedGroups->sum('total_invoice_amount'),
            'average_commission_percentage' => 0,
            'groups_by_percentage_range' => [],
            'top_discrepancies' => [],
            'monthly_summary' => [],
            'agent_summary' => []
        ];

        if ($matchedGroups->isNotEmpty()) {
            // Calculate average percentage
            $validPercentages = $matchedGroups->filter(function ($group) {
                return $group->commission_percentage && $group->commission_percentage < 1000;  // Filter out extreme values
            });

            if ($validPercentages->isNotEmpty()) {
                $analysis['average_commission_percentage'] = $validPercentages->avg('commission_percentage');
            }

            // Group by percentage ranges
            $analysis['groups_by_percentage_range'] = $matchedGroups->groupBy(function ($group) {
                $pct = $group->commission_percentage ?? 0;
                if ($pct < 10)
                    return '0-10%';
                if ($pct < 25)
                    return '10-25%';
                if ($pct < 50)
                    return '25-50%';
                if ($pct < 100)
                    return '50-100%';
                if ($pct < 200)
                    return '100-200%';
                return '200%+';
            })->map->count();

            // Find top discrepancies (highest percentages)
            $analysis['top_discrepancies'] = $matchedGroups
                ->sortByDesc('commission_percentage')
                ->take(10)
                ->map(function ($group) {
                    return [
                        'agent_id' => $group->agent_id,
                        'invoice_at' => $group->invoice_at->format('Y-m-d'),
                        'commission_amount' => $group->total_commission_amount,
                        'invoice_amount' => $group->total_invoice_amount,
                        'percentage' => $group->commission_percentage,
                        'invoice_number' => $group->purchaseInvoice->number ?? 'N/A'
                    ];
                });

            // Monthly summary
            $analysis['monthly_summary'] = $matchedGroups
                ->groupBy(function ($group) {
                    return $group->invoice_at->format('Y-m');
                })
                ->map(function ($groups, $month) {
                    return [
                        'month' => $month,
                        'groups_count' => $groups->count(),
                        'total_commission' => $groups->sum('total_commission_amount'),
                        'total_invoice' => $groups->sum('total_invoice_amount'),
                        'avg_percentage' => $groups->avg('commission_percentage')
                    ];
                })
                ->sortBy('month');

            // Agent summary (only for agents that exist)
            $validAgentIds = Agent::pluck('id')->toArray();
            $analysis['agent_summary'] = $matchedGroups
                ->whereIn('agent_id', $validAgentIds)
                ->groupBy('agent_id')
                ->map(function ($groups, $agentId) {
                    $agent = Agent::find($agentId);
                    return [
                        'agent_id' => $agentId,
                        'agent_name' => $agent->name ?? 'Unknown',
                        'groups_count' => $groups->count(),
                        'total_commission' => $groups->sum('total_commission_amount'),
                        'total_invoice' => $groups->sum('total_invoice_amount'),
                        'avg_percentage' => $groups->avg('commission_percentage')
                    ];
                })
                ->sortByDesc('total_commission');
        }

        return $analysis;
    }

    protected function displayAnalysis($analysis)
    {
        $this->newLine();
        $this->info('Commission-Invoice Match Analysis');
        $this->info('===================================');

        $this->line("Total matched groups: {$analysis['total_matched']}");
        $this->line('Total commission amount: €' . number_format($analysis['total_commission_amount'], 2));
        $this->line('Total invoice amount: €' . number_format($analysis['total_invoice_amount'], 2));
        $this->line('Average commission percentage: ' . number_format($analysis['average_commission_percentage'], 2) . '%');

        // Percentage ranges
        $this->newLine();
        $this->info('Distribution by Commission Percentage:');
        foreach ($analysis['groups_by_percentage_range'] as $range => $count) {
            $percentage = ($count / $analysis['total_matched']) * 100;
            $this->line("  {$range}: {$count} groups (" . number_format($percentage, 1) . '%)');
        }

        // Top discrepancies
        if (!empty($analysis['top_discrepancies'])) {
            $this->newLine();
            $this->info('Top Discrepancies (Highest %):');
            foreach ($analysis['top_discrepancies'] as $discrepancy) {
                $this->line(sprintf(
                    'Agent %s - %s: €%s vs €%s (%.1f%%) - Invoice %s',
                    $discrepancy['agent_id'],
                    $discrepancy['invoice_at'],
                    number_format($discrepancy['commission_amount'], 2),
                    number_format($discrepancy['invoice_amount'], 2),
                    $discrepancy['percentage'],
                    $discrepancy['invoice_number']
                ));
            }
        }

        // Monthly summary
        if (!empty($analysis['monthly_summary'])) {
            $this->newLine();
            $this->info('Monthly Summary:');
            foreach ($analysis['monthly_summary'] as $month) {
                $this->line(sprintf(
                    '%s: %d groups, €%s commission, €%s invoice (%.1f%%)',
                    $month['month'],
                    $month['groups_count'],
                    number_format($month['total_commission'], 2),
                    number_format($month['total_invoice'], 2),
                    $month['avg_percentage']
                ));
            }
        }

        // Agent summary
        if (!empty($analysis['agent_summary'])) {
            $this->newLine();
            $this->info('Top Agents by Commission:');
            foreach ($analysis['agent_summary']->take(10) as $agent) {
                $this->line(sprintf(
                    '%s (ID: %s): %d groups, €%s total (%.1f%% avg)',
                    $agent['agent_name'],
                    $agent['agent_id'],
                    $agent['groups_count'],
                    number_format($agent['total_commission'], 2),
                    $agent['avg_percentage']
                ));
            }
        }
    }

    protected function exportToCsv($analysis)
    {
        $filename = 'commission_invoice_analysis_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = storage_path($filename);

        $handle = fopen($filepath, 'w');

        // Header
        fputcsv($handle, [
            'Agent ID',
            'Agent Name',
            'Invoice Date',
            'Commission Amount',
            'Invoice Amount',
            'Percentage',
            'Invoice Number'
        ]);

        // Data
        foreach ($analysis['top_discrepancies'] as $discrepancy) {
            fputcsv($handle, [
                $discrepancy['agent_id'],
                'Unknown Agent',  // We don't have agent names in discrepancies
                $discrepancy['invoice_at'],
                $discrepancy['commission_amount'],
                $discrepancy['invoice_amount'],
                $discrepancy['percentage'],
                $discrepancy['invoice_number']
            ]);
        }

        fclose($handle);

        $this->info("Export saved to: {$filepath}");
    }
}
