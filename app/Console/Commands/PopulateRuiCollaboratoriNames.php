<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopulateRuiCollaboratoriNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rui:populate-collaboratori-names
                            {--batch=1000 : Number of records to process in each batch}
                            {--force : Force update even if names are already populated}
                            {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate names in rui_collaboratori by joining with rui table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Populating names in rui_collaboratori...');

        $batchSize = (int) $this->option('batch');
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');

        // Check if there are records to process
        $totalRecords = DB::table('rui_collaboratori')->count();
        $emptyNames = DB::table('rui_collaboratori')
            ->whereNull('intermediario')
            ->orWhere('intermediario', '')
            ->count();

        $this->info("📊 Total collaboratori records: {$totalRecords}");
        $this->info("📝 Records with empty names: {$emptyNames}");

        if ($emptyNames === 0 && !$force) {
            $this->info('✅ All records already have names populated! Use --force to update anyway.');
            return 0;
        }

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');

            // Show sample of what would be updated
            $this->line('Sample records that would be updated:');

            // Sample for intermediario
            $sample1 = DB::table('rui_collaboratori as c')
                ->join('rui as ri', 'ri.numero_iscrizione_rui', '=', 'c.num_iscr_intermediario')
                ->select('c.num_iscr_intermediario', 'c.intermediario', 'ri.cognome_nome', 'ri.ragione_sociale')
                ->where(function ($query) {
                    $query
                        ->whereNull('c.intermediario')
                        ->orWhere('c.intermediario', '');
                })
                ->limit(2)
                ->get();

            $this->line('  Intermediario updates:');
            foreach ($sample1 as $record) {
                $newName = trim(($record->cognome_nome ?? '') . ' ' . ($record->ragione_sociale ?? ''));
                $this->line("    {$record->num_iscr_intermediario}: '{$record->intermediario}' -> '{$newName}'");
            }

            // Sample for collaboratore
            $sample2 = DB::table('rui_collaboratori as c')
                ->join('rui as rc', 'rc.numero_iscrizione_rui', '=', 'c.num_iscr_collaboratori_i_liv')
                ->select('c.num_iscr_collaboratori_i_liv', 'c.collaboratore', 'rc.cognome_nome', 'rc.ragione_sociale')
                ->where(function ($query) {
                    $query
                        ->whereNull('c.collaboratore')
                        ->orWhere('c.collaboratore', '');
                })
                ->limit(2)
                ->get();

            $this->line('  Collaboratore updates:');
            foreach ($sample2 as $record) {
                $newName = trim(($record->cognome_nome ?? '') . ' ' . ($record->ragione_sociale ?? ''));
                $this->line("    {$record->num_iscr_collaboratori_i_liv}: '{$record->collaboratore}' -> '{$newName}'");
            }

            // Sample for dipendente
            $sample3 = DB::table('rui_collaboratori as c')
                ->join('rui as rd', 'rd.numero_iscrizione_rui', '=', 'c.num_iscr_collaboratori_ii_liv')
                ->select('c.num_iscr_collaboratori_ii_liv', 'c.dipendente', 'rd.cognome_nome', 'rd.ragione_sociale')
                ->where(function ($query) {
                    $query
                        ->whereNull('c.dipendente')
                        ->orWhere('c.dipendente', '');
                })
                ->limit(2)
                ->get();

            $this->line('  Dipendente updates:');
            foreach ($sample3 as $record) {
                $newName = trim(($record->cognome_nome ?? '') . ' ' . ($record->ragione_sociale ?? ''));
                $this->line("    {$record->num_iscr_collaboratori_ii_liv}: '{$record->dipendente}' -> '{$newName}'");
            }
            return 0;
        }

        // Update names using JOIN with rui table
        $this->line('🔄 Updating names...');

        $startTime = microtime(true);

        if ($force) {
            // Update all records
            $this->info("🔄 Force updating all {$totalRecords} records...");

            // Update intermediario names
            $updated1 = DB::statement("
                UPDATE rui_collaboratori AS c
                INNER JOIN rui AS ri ON ri.numero_iscrizione_rui = c.num_iscr_intermediario
                SET c.intermediario = CONCAT(
                    COALESCE(ri.cognome_nome, ''),
                    COALESCE(ri.ragione_sociale, '')
                )
            ");

            // Update collaboratore names
            $updated2 = DB::statement("
                UPDATE rui_collaboratori AS c
                INNER JOIN rui AS rc ON rc.numero_iscrizione_rui = c.num_iscr_collaboratori_i_liv
                SET c.collaboratore = CONCAT(
                    COALESCE(rc.cognome_nome, ''),
                    COALESCE(rc.ragione_sociale, '')
                )
            ");

            // Update dipendente names
            $updated3 = DB::statement("
                UPDATE rui_collaboratori AS c
                INNER JOIN rui AS rd ON rd.numero_iscrizione_rui = c.num_iscr_collaboratori_ii_liv
                SET c.dipendente = CONCAT(
                    COALESCE(rd.cognome_nome, ''),
                    COALESCE(rd.ragione_sociale, '')
                )
            ");

            $updated = $updated1 && $updated2 && $updated3;
        } else {
            // Update only empty names
            $this->info('🔄 Updating empty names...');

            // Update intermediario names
            $updated1 = DB::statement("
                UPDATE rui_collaboratori AS c
                INNER JOIN rui AS ri ON ri.numero_iscrizione_rui = c.num_iscr_intermediario
                SET c.intermediario = CONCAT(
                    COALESCE(ri.cognome_nome, ''),
                    COALESCE(ri.ragione_sociale, '')
                )
                WHERE c.intermediario IS NULL OR c.intermediario = ''
            ");

            // Update collaboratore names
            $updated2 = DB::statement("
                UPDATE rui_collaboratori AS c
                INNER JOIN rui AS rc ON rc.numero_iscrizione_rui = c.num_iscr_collaboratori_i_liv
                SET c.collaboratore = CONCAT(
                    COALESCE(rc.cognome_nome, ''),
                    COALESCE(rc.ragione_sociale, '')
                )
                WHERE c.collaboratore IS NULL OR c.collaboratore = ''
            ");

            // Update dipendente names
            $updated3 = DB::statement("
                UPDATE rui_collaboratori AS c
                INNER JOIN rui AS rd ON rd.numero_iscrizione_rui = c.num_iscr_collaboratori_ii_liv
                SET c.dipendente = CONCAT(
                    COALESCE(rd.cognome_nome, ''),
                    COALESCE(rd.ragione_sociale, '')
                )
                WHERE c.dipendente IS NULL OR c.dipendente = ''
            ");

            $updated = $updated1 && $updated2 && $updated3;
        }

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);

        if ($updated) {
            $processedCount = $force ? $totalRecords : $emptyNames;
            $this->info("✅ Successfully updated {$processedCount} records in {$executionTime} seconds");

            // Verify update
            $remainingEmpty1 = DB::table('rui_collaboratori')
                ->whereNull('intermediario')
                ->orWhere('intermediario', '')
                ->count();

            $remainingEmpty2 = DB::table('rui_collaboratori')
                ->whereNull('collaboratore')
                ->orWhere('collaboratore', '')
                ->count();

            $remainingEmpty3 = DB::table('rui_collaboratori')
                ->whereNull('dipendente')
                ->orWhere('dipendente', '')
                ->count();

            $totalRemaining = $remainingEmpty1 + $remainingEmpty2 + $remainingEmpty3;

            if ($totalRemaining === 0) {
                $this->info('🎉 All names populated successfully!');
            } else {
                $this->warn("⚠️  {$totalRemaining} records still have empty names:");
                if ($remainingEmpty1 > 0)
                    $this->info("    - {$remainingEmpty1} intermediario fields");
                if ($remainingEmpty2 > 0)
                    $this->info("    - {$remainingEmpty2} collaboratore fields");
                if ($remainingEmpty3 > 0)
                    $this->info("    - {$remainingEmpty3} dipendente fields");
            }
        } else {
            $this->error('❌ Failed to update records');
            return 1;
        }

        $this->newLine();
        $this->info('✅ RUI collaboratori names population completed!');
        return 0;
    }
}
