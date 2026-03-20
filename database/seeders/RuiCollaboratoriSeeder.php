<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuiCollaboratoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Populating names in rui_collaboratori...');

        // Check if there are records to process
        $totalRecords = DB::table('rui_collaboratori')->count();
        $emptyNames = DB::table('rui_collaboratori')
            ->where(function ($query) {
                $query
                    ->whereNull('intermediario')
                    ->orWhere('intermediario', '');
            })
            ->orWhere(function ($query) {
                $query
                    ->whereNull('collaboratore')
                    ->orWhere('collaboratore', '');
            })
            ->orWhere(function ($query) {
                $query
                    ->whereNull('dipendente')
                    ->orWhere('dipendente', '');
            })
            ->count();

        $this->command->info("📊 Total collaboratori records: {$totalRecords}");
        $this->command->info("📝 Records with empty names: {$emptyNames}");

        if ($emptyNames === 0) {
            $this->command->info('✅ All records already have names populated!');
            return;
        }

        // Update names using JOIN with rui table
        $this->command->line('🔄 Updating names...');

        $startTime = microtime(true);

        // Update intermediario names
        $updated1 = DB::statement("
            UPDATE rui_collaboratori AS c
            INNER JOIN rui AS ri ON ri.numero_iscrizione_rui = c.num_iscr_intermediario
            SET c.intermediario = TRIM(CONCAT(
                COALESCE(ri.cognome_nome, ''),
                ' ',
                COALESCE(ri.ragione_sociale, '')
            ))
            WHERE c.intermediario IS NULL OR c.intermediario = ''
        ");

        // Update collaboratore names
        $updated2 = DB::statement("
            UPDATE rui_collaboratori AS c
            INNER JOIN rui AS rc ON rc.numero_iscrizione_rui = c.num_iscr_collaboratori_i_liv
            SET c.collaboratore = TRIM(CONCAT(
                COALESCE(rc.cognome_nome, ''),
                ' ',
                COALESCE(rc.ragione_sociale, '')
            ))
            WHERE c.collaboratore IS NULL OR c.collaboratore = ''
        ");

        // Update dipendente names
        $updated3 = DB::statement("
            UPDATE rui_collaboratori AS c
            INNER JOIN rui AS rd ON rd.numero_iscrizione_rui = c.num_iscr_collaboratori_ii_liv
            SET c.dipendente = TRIM(CONCAT(
                COALESCE(rd.cognome_nome, ''),
                ' ',
                COALESCE(rd.ragione_sociale, '')
            ))
            WHERE c.dipendente IS NULL OR c.dipendente = ''
        ");

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);

        if ($updated1 && $updated2 && $updated3) {
            $this->command->info("✅ Successfully updated records in {$executionTime} seconds");

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
                $this->command->info('🎉 All names populated successfully!');
            } else {
                $this->command->warn("⚠️  {$totalRemaining} records still have empty names:");
                if ($remainingEmpty1 > 0)
                    $this->command->info("    - {$remainingEmpty1} intermediario fields");
                if ($remainingEmpty2 > 0)
                    $this->command->info("    - {$remainingEmpty2} collaboratore fields");
                if ($remainingEmpty3 > 0)
                    $this->command->info("    - {$remainingEmpty3} dipendente fields");
            }
        } else {
            $this->command->error('❌ Failed to update records');
            return;
        }

        $this->command->newLine();
        $this->command->info('✅ RUI collaboratori names population completed!');
    }
}
