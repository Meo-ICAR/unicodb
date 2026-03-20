<?php

namespace App\Console\Commands;

use App\Models\AuiLog;
use App\Models\AuiRecord;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConsolidaAuiRecords extends Command
{
    protected $signature = 'aui:consolidamento';
    protected $description = 'Consolida i log AUI in record ufficiali prima della scadenza dei 30 giorni.';

    public function handle()
    {
        // Peschiamo i log vecchi di almeno 25 giorni per evitare scadenze
        $dataLimite = Carbon::now()->subDays(25);

        $logsDaConsolidare = AuiLog::where('stato', 'da_consolidare')
            ->whereDate('data_evento', '<=', $dataLimite)
            ->orderBy('data_evento', 'asc')  // FONDAMENTALE PER L'ORDINE CRONOLOGICO
            ->get();

        if ($logsDaConsolidare->isEmpty()) {
            $this->info('Nessun log AUI richiede consolidamento urgente oggi.');
            return;
        }

        DB::beginTransaction();
        try {
            $annoCorrente = date('Y');
            $ultimoProgressivo = AuiRecord::whereYear('created_at', $annoCorrente)->count();

            foreach ($logsDaConsolidare as $log) {
                $ultimoProgressivo++;
                $codiceAui = 'AUI-' . $annoCorrente . '-' . str_pad($ultimoProgressivo, 4, '0', STR_PAD_LEFT);

                // 1. Creiamo il record ufficiale e immutabile
                AuiRecord::create([
                    'aui_log_id' => $log->id,
                    'pratica_id' => $log->pratica_id,
                    'codice_univoco_aui' => $codiceAui,
                    'tipo_registrazione' => $log->tipo_evento,
                    'data_registrazione' => $log->data_evento,
                    'importo_operazione' => $log->importo_rilevato,
                ]);

                // 2. Aggiorniamo lo stato del log temporaneo
                $log->update(['stato' => 'consolidato']);
            }

            DB::commit();
            $this->info("Consolidati con successo {$logsDaConsolidare->count()} record AUI.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Errore durante il consolidamento: ' . $e->getMessage());
        }
    }
}
