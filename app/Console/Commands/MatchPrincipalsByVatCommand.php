<?php

namespace App\Console\Commands;

use App\Models\PracticeCommission;
use App\Models\Principal;
use App\Models\SalesInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatchPrincipalsByVatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:match-principals-by-vat {--company= : Company ID to process} {--force : Force update existing matches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Match practice commissions with sales invoices using VAT number';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting VAT-based principal matching...');

        $companyId = $this->option('company');
        $force = $this->option('force');

        $this->info('Company ID: ' . ($companyId ?: 'All'));
        $this->info('Force update: ' . ($force ? 'YES' : 'NO'));

        // Get sales invoices with VAT number
        $salesInvoicesQuery = SalesInvoice::whereNotNull('vat_number');

        if ($companyId) {
            $salesInvoicesQuery->where('company_id', $companyId);
        }

        $salesInvoices = $salesInvoicesQuery->get();
        $this->info("Found {$salesInvoices->count()} sales invoices with VAT number");

        $matches = 0;
        $updates = 0;

        foreach ($salesInvoices as $salesInvoice) {
            // Find principal by VAT number first, then by name
            $principal = null;

            if ($salesInvoice->vat_number) {
                $principal = Principal::where('vat_number', $salesInvoice->vat_number)->first();
            }

            // If not found by VAT, try by name with multiple matching strategies
            if (!$principal && $salesInvoice->customer_name) {
                $customerName = trim($salesInvoice->customer_name);

                // 1. Exact match
                $principal = Principal::where('name', $customerName)->first();

                // 2. Case-insensitive match
                if (!$principal) {
                    $principal = Principal::whereRaw('LOWER(name) = ?', [strtolower($customerName)])->first();
                }

                // 3. Remove common suffixes and try again
                if (!$principal) {
                    $cleanedName = $customerName;
                    $cleanedName = preg_replace('/\s+(S\.P\.A\.|SPA|S\.R\.L\.|SRL)$/i', '', $cleanedName);
                    $cleanedName = trim($cleanedName);

                    $principal = Principal::whereRaw('LOWER(name) = ?', [strtolower($cleanedName)])->first();

                    // Try with common suffixes
                    if (!$principal) {
                        $suffixes = ['S.P.A.', 'SPA', 'S.R.L.', 'SRL'];
                        foreach ($suffixes as $suffix) {
                            $testName = $cleanedName . ' ' . $suffix;
                            $principal = Principal::whereRaw('LOWER(name) = ?', [strtolower($testName)])->first();
                            if ($principal)
                                break;
                        }
                    }
                }

                // 4. Partial match (contains)
                if (!$principal) {
                    $principal = Principal::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($customerName) . '%'])
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($cleanedName) . '%'])
                        ->first();
                }
            }

            if (!$principal) {
                $this->line("No principal found for: {$salesInvoice->customer_name} (VAT: {$salesInvoice->vat_number})");
                continue;
            }

            // Find practice commissions for this principal with extended date range
            $targetDate = $salesInvoice->registration_date;
            $startDate = $targetDate->copy()->subDays(30);  // Extended to 30 days before
            $endDate = $targetDate->copy()->addDays(30);  // Extended to 30 days after

            $commissionsQuery = PracticeCommission::where('tipo', 'Istituto')
                ->where('principal_id', $principal->id)
                ->whereDate('invoice_at', '>=', $startDate)
                ->whereDate('invoice_at', '<=', $endDate);

            if ($companyId) {
                $commissionsQuery->where('company_id', $companyId);
            }

            // Only update if alternative_number_invoice is null or force is true
            if (!$force) {
                $commissionsQuery->whereNull('alternative_number_invoice');
            }

            $commissions = $commissionsQuery->get();

            if ($commissions->isEmpty()) {
                $this->line("No commissions found for principal {$principal->name} around {$targetDate->format('Y-m-d')}");
                continue;
            }

            // Update commissions with sales invoice number
            foreach ($commissions as $commission) {
                $commission->update([
                    'alternative_number_invoice' => $salesInvoice->number
                ]);
                $updates++;

                $matches++;
                $this->line("Updated commission ID {$commission->id}: {$salesInvoice->number} (Principal: {$principal->name})");
            }
        }

        $this->info('VAT-based Principal Matching Results:');
        $this->info('====================================');
        $this->info("Sales invoices processed: {$salesInvoices->count()}");
        $this->info("Matches found: {$matches}");
        $this->info("Commissions updated: {$updates}");

        Log::info('VAT-based principal matching completed', [
            'company_id' => $companyId,
            'sales_invoices_processed' => $salesInvoices->count(),
            'matches_found' => $matches,
            'commissions_updated' => $updates,
            'force_update' => $force
        ]);

        $this->info('VAT-based matching completed successfully!');
    }
}
