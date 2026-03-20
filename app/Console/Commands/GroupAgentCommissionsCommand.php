<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\AgentCommissionGroup;
use App\Models\PracticeCommission;
use App\Models\PurchaseInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroupAgentCommissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:group-agent {--company= : Company ID to filter} {--date= : Specific date to process (YYYY-MM-DD)} {--recreate : Delete existing groups and recreate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Group agent commissions by agent and invoice date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companyId = $this->option('company');
        $specificDate = $this->option('date');
        $recreate = $this->option('recreate');

        $this->info('Starting agent commission grouping...');
        $this->info('Company ID: ' . ($companyId ?: 'All'));
        $this->info('Date: ' . ($specificDate ?: 'All dates'));
        $this->info('Recreate: ' . ($recreate ? 'YES' : 'NO'));

        try {
            $results = $this->performGrouping($companyId, $specificDate, $recreate);

            $this->displayResults($results);

            return 0;
        } catch (\Exception $e) {
            $this->error('Grouping failed: ' . $e->getMessage());
            Log::error('Agent commission grouping failed', [
                'company_id' => $companyId,
                'error' => $e->getMessage()
            ]);
            return 1;
        }
    }

    protected function performGrouping($companyId, $specificDate, $recreate)
    {
        $results = [
            'total_commissions' => 0,
            'groups_created' => 0,
            'groups_updated' => 0,
            'matches_found' => 0,
            'unmatched_groups' => 0
        ];

        // Delete existing groups if recreate option is used
        if ($recreate) {
            $query = AgentCommissionGroup::query();
            if ($companyId) {
                $query->where('company_id', $companyId);
            }
            if ($specificDate) {
                $query->whereDate('invoice_at', $specificDate);
            }
            $deleted = $query->delete();
            $this->info("Deleted {$deleted} existing groups");
        }

        // Get agent commissions (tipo = 'Agente')
        $commissionsQuery = PracticeCommission::where('tipo', 'Agente')
            ->whereNotNull('agent_id')
            ->whereNotNull('invoice_at')
            ->with(['company']);

        // Also check for alternative_number_invoice if invoice_number is null
        $commissionsQuery->where(function ($query) {
            $query
                ->whereNotNull('invoice_number')
                ->orWhereNotNull('alternative_number_invoice');
        });

        if ($companyId) {
            $commissionsQuery->where('company_id', $companyId);
        }
        if ($specificDate) {
            $commissionsQuery->whereDate('invoice_at', $specificDate);
        }

        $commissions = $commissionsQuery->get();
        $results['total_commissions'] = $commissions->count();

        if ($commissions->isEmpty()) {
            $this->warn('No agent commissions found');
            return $results;
        }

        // Group by agent_id, company_id, and invoice_at
        $groupedCommissions = $commissions->groupBy(function ($commission) {
            return $commission->company_id . '_' . $commission->agent_id . '_' . $commission->invoice_at->format('Y-m-d');
        });

        $this->info("Found {$groupedCommissions->count()} commission groups to process");

        // Progress bar
        $progressBar = $this->output->createProgressBar($groupedCommissions->count());
        $progressBar->start();

        foreach ($groupedCommissions as $groupKey => $commissionGroup) {
            $firstCommission = $commissionGroup->first();
            $totalAmount = $commissionGroup->sum('amount');

            $groupData = [
                'company_id' => $firstCommission->company_id,
                'agent_id' => $firstCommission->agent_id,
                'invoice_at' => $firstCommission->invoice_at,
                'total_commission_amount' => $totalAmount,
                'total_invoice_amount' => null,
                'commission_percentage' => null,
                'purchase_invoice_id' => null,
                'is_matched' => false,
                'notes' => "Grouped from {$commissionGroup->count()} commission records"
            ];

            // Check if group already exists
            $existingGroup = AgentCommissionGroup::where('company_id', $groupData['company_id'])
                ->where('agent_id', $groupData['agent_id'])
                ->whereDate('invoice_at', $groupData['invoice_at'])
                ->first();

            if ($existingGroup) {
                // Update existing group
                $existingGroup->update($groupData);
                $group = $existingGroup;
                $results['groups_updated']++;
            } else {
                // Create new group
                $group = AgentCommissionGroup::create($groupData);
                $results['groups_created']++;
            }

            // Try to match with purchase invoice
            $this->matchWithPurchaseInvoice($group);
            if ($group->is_matched) {
                $results['matches_found']++;
            } else {
                $results['unmatched_groups']++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        return $results;
    }

    protected function matchWithPurchaseInvoice($group)
    {
        // Try to find matching purchase invoice
        $matchingInvoice = PurchaseInvoice::where('company_id', $group->company_id)
            ->whereDate('document_date', $group->invoice_at)
            ->first();

        if ($matchingInvoice) {
            // Get the invoice number from the commission
            $invoiceNumber = null;
            $commissions = PracticeCommission::where('agent_id', $group->agent_id)
                ->whereDate('invoice_at', $group->invoice_at)
                ->where('company_id', $group->company_id)
                ->get();

            foreach ($commissions as $commission) {
                if ($commission->invoice_number) {
                    $invoiceNumber = $commission->invoice_number;
                    break;
                } elseif ($commission->alternative_number_invoice) {
                    $invoiceNumber = $commission->alternative_number_invoice;
                    break;
                }
            }

            $group->update([
                'total_invoice_amount' => $matchingInvoice->amount,
                'purchase_invoice_id' => $matchingInvoice->id,
                'number_invoice' => $invoiceNumber,
                'is_matched' => true,
                'notes' => $group->notes . " | Matched with purchase invoice {$matchingInvoice->number}"
            ]);

            // Calculate commission percentage
            $group->updateCommissionPercentage();

            Log::info('Commission group matched with purchase invoice', [
                'group_id' => $group->id,
                'agent_id' => $group->agent_id,
                'invoice_at' => $group->invoice_at,
                'purchase_invoice_id' => $matchingInvoice->id,
                'commission_amount' => $group->total_commission_amount,
                'invoice_amount' => $matchingInvoice->amount
            ]);
        }
    }

    protected function displayResults($results)
    {
        $this->newLine();
        $this->info('Grouping Results:');
        $this->info('================');
        $this->line("Total commissions processed: {$results['total_commissions']}");
        $this->line("Groups created: {$results['groups_created']}");
        $this->line("Groups updated: {$results['groups_updated']}");
        $this->line("Matches with invoices: {$results['matches_found']}");
        $this->line("Unmatched groups: {$results['unmatched_groups']}");

        // Show some statistics
        $totalGroups = $results['groups_created'] + $results['groups_updated'];
        if ($totalGroups > 0) {
            $matchRate = ($results['matches_found'] / $totalGroups) * 100;
            $this->line('Match rate: ' . number_format($matchRate, 1) . '%');
        }

        // Show top matched groups
        $topGroups = AgentCommissionGroup::with(['agent', 'purchaseInvoice'])
            ->matched()
            ->orderBy('total_commission_amount', 'desc')
            ->take(5)
            ->get();

        if ($topGroups->isNotEmpty()) {
            $this->newLine();
            $this->info('Top Matched Groups:');
            foreach ($topGroups as $group) {
                $this->line(sprintf(
                    '%s - %s: €%s (Invoice: €%s, %.1f%%)',
                    $group->agent->name ?? 'Unknown Agent',
                    $group->invoice_at->format('Y-m-d'),
                    number_format($group->total_commission_amount, 2),
                    number_format($group->total_invoice_amount ?? 0, 2),
                    $group->commission_percentage ?? 0
                ));
            }
        }
    }
}
