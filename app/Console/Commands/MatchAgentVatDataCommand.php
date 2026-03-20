<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\PurchaseInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatchAgentVatDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agents:match-vat-data {--company= : Company ID to filter} {--dry-run : Show matches without updating} {--threshold=80 : Similarity threshold percentage}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Match agents with purchase invoices and update VAT data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $companyId = $this->option('company');
        $dryRun = $this->option('dry-run');
        $threshold = (int) $this->option('threshold');

        $this->info('Starting agent-vat data matching...');
        $this->info('Company ID: ' . ($companyId ?: 'All'));
        $this->info('Dry run: ' . ($dryRun ? 'YES' : 'NO'));
        $this->info("Similarity threshold: {$threshold}%");

        try {
            $results = $this->performMatching($companyId, $threshold, $dryRun);

            $this->displayResults($results);

            return 0;
        } catch (\Exception $e) {
            $this->error('Matching failed: ' . $e->getMessage());
            Log::error('Agent VAT matching failed', [
                'company_id' => $companyId,
                'error' => $e->getMessage()
            ]);
            return 1;
        }
    }

    protected function performMatching($companyId, $threshold, $dryRun)
    {
        $results = [
            'total_agents' => 0,
            'matched_agents' => 0,
            'updated_agents' => 0,
            'unmatched_agents' => 0,
            'matches' => []
        ];

        // Get agents
        $agentsQuery = Agent::with('company');
        if ($companyId) {
            $agentsQuery->where('company_id', $companyId);
        }
        $agents = $agentsQuery->get();

        $results['total_agents'] = $agents->count();

        if ($agents->isEmpty()) {
            $this->warn('No agents found');
            return $results;
        }

        // Get unique suppliers from purchase invoices
        $suppliersQuery = PurchaseInvoice::select('supplier', 'vat_number', 'company_id')
            ->whereNotNull('supplier')
            ->whereNotNull('vat_number')
            ->where('vat_number', '!=', '')
            ->distinct();

        if ($companyId) {
            $suppliersQuery->where('company_id', $companyId);
        }

        $suppliers = $suppliersQuery->get();

        $this->info("Found {$suppliers->count()} unique suppliers with VAT numbers");

        // Progress bar
        $progressBar = $this->output->createProgressBar($agents->count());
        $progressBar->start();

        foreach ($agents as $agent) {
            $bestMatch = null;
            $bestScore = 0;

            foreach ($suppliers as $supplier) {
                $score = $this->calculateSimilarity($agent->name, $supplier->supplier);

                if ($score > $bestScore && $score >= $threshold) {
                    $bestScore = $score;
                    $bestMatch = $supplier;
                }
            }

            if ($bestMatch) {
                $results['matched_agents']++;

                $matchData = [
                    'agent_id' => $agent->id,
                    'agent_name' => $agent->name,
                    'supplier_name' => $bestMatch->supplier,
                    'similarity_score' => $bestScore,
                    'vat_number' => $bestMatch->vat_number,
                    'current_vat_number' => $agent->vat_number,
                    'current_vat_name' => $agent->vat_name,
                    'needs_update' => $this->needsUpdate($agent, $bestMatch)
                ];

                $results['matches'][] = $matchData;

                if (!$dryRun && $matchData['needs_update']) {
                    $this->updateAgentVatData($agent, $bestMatch);
                    $results['updated_agents']++;
                }
            } else {
                $results['unmatched_agents']++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        return $results;
    }

    protected function calculateSimilarity($string1, $string2)
    {
        // Normalize strings: trim, lowercase, remove multiple spaces
        $normalize = function ($str) {
            $str = trim(strtolower($str));
            $str = preg_replace('/\s+/', ' ', $str);
            return $str;
        };

        $str1 = $normalize($string1);
        $str2 = $normalize($string2);

        // Use Levenshtein distance for similarity
        $levenshteinDistance = levenshtein($str1, $str2);
        $maxLength = max(strlen($str1), strlen($str2));

        if ($maxLength === 0) {
            return 100;
        }

        $similarity = (1 - $levenshteinDistance / $maxLength) * 100;

        // Also check for substring matches
        $substringBonus = 0;
        if (strpos($str1, $str2) !== false || strpos($str2, $str1) !== false) {
            $substringBonus = 20;
        }

        return min(100, $similarity + $substringBonus);
    }

    protected function needsUpdate($agent, $supplier)
    {
        // Check if VAT data needs updating
        $needsVatNumber = empty($agent->vat_number) || $agent->vat_number !== $supplier->vat_number;
        $needsVatName = empty($agent->vat_name) || strtolower(trim($agent->vat_name)) !== strtolower(trim($supplier->supplier));

        return $needsVatNumber || $needsVatName;
    }

    protected function updateAgentVatData($agent, $supplier)
    {
        $agent->update([
            'vat_number' => $supplier->vat_number,
            'vat_name' => $supplier->supplier
        ]);

        Log::info('Agent VAT data updated', [
            'agent_id' => $agent->id,
            'agent_name' => $agent->name,
            'new_vat_number' => $supplier->vat_number,
            'new_vat_name' => $supplier->supplier
        ]);
    }

    protected function displayResults($results)
    {
        $this->newLine();
        $this->info('Matching Results:');
        $this->info('================');
        $this->line("Total agents: {$results['total_agents']}");
        $this->line("Matched agents: {$results['matched_agents']}");
        $this->line("Updated agents: {$results['updated_agents']}");
        $this->line("Unmatched agents: {$results['unmatched_agents']}");

        if (!empty($results['matches'])) {
            $this->newLine();
            $this->info('Top Matches:');

            // Sort by similarity score
            $matches = collect($results['matches'])->sortByDesc('similarity_score')->take(10);

            foreach ($matches as $match) {
                $status = $match['needs_update'] ? 'UPDATE' : 'NO CHANGE';
                $this->line(sprintf(
                    '[%s] %s ↔ %s (%.1f%%) - VAT: %s',
                    $status,
                    $match['agent_name'],
                    $match['supplier_name'],
                    $match['similarity_score'],
                    $match['vat_number']
                ));
            }
        }
    }
}
