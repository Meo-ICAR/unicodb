<?php

namespace App\Observers;

use App\Models\AuiLog;
use App\Models\Practice;

class PracticeObserver
{
    public function saved(Practice $practice): void
    {
        // ESECUZIONE OPERAZIONE (Quando la singola richiesta in banca passa a "erogata")
        if ($practice->wasChanged('erogated_at')) {
            // 1. Registriamo l'esecuzione in AUI
            $this->registraEsecuzioneAui($practice);

            // 2. AUTOMAZIONE MAGICA: Se questa practice è erogata, chiudiamo il mandato padre!
            // (Questo triggererà automaticamente il ClientMandateObserver per fare la Chiusura AUI)
            if ($practice->mandato && $practice->mandato->stato !== 'concluso_con_successo') {
                $practice->mandato->update(['stato' => 'concluso_con_successo']);
            }
        }
    }

    private function registraEsecuzioneAui(Practice $practice): void
    {
        $esiste = AuiLog::where('practice_id', $practice->id)
            ->where('tipo_evento', 'esecuzione_operazione')
            ->exists();

        if (!$esiste) {
            AuiLog::create([
                'client_mandate_id' => $practice->client_mandate_id,
                'practice_id' => $practice->id,  // Qui salviamo l'ID della practice vincente!
                'tipo_evento' => 'esecuzione_operazione',
                // Ricorda: per i mutui è la data_stipula, per le cessioni è data_liquidazione
                'data_evento' => $practice->data_erogazione,
                'importo_rilevato' => $practice->importo_erogato,
                'stato' => 'da_consolidare',
                'payload_dati_cliente' => $practice->mandato->cliente->toArray(),
            ]);
        }
    }
}
