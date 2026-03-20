<?php

namespace App\Observers;

use App\Models\AuiLog;
use App\Models\ClientMandate;
use Carbon\Carbon;

class ClientMandateObserver
{
    public function saved(ClientMandate $mandato): void
    {
        // 1. INSTAURAZIONE DEL RAPPORTO (Quando viene inserita la data firma)
        if ($mandato->wasChanged('data_firma_mandato')) {
            if ($mandato->data_firma_mandato) {
                $this->registraLogAui(
                    $mandato,
                    'instaurazione_rapporto',
                    $mandato->data_firma_mandato,
                    $mandato->importo_richiesto_mandato
                );
            }
        }

        // 2. CHIUSURA DEL RAPPORTO (Quando scade, viene revocato, o si conclude con successo)
        if ($mandato->wasChanged('stato') && in_array($mandato->stato, ['concluso_con_successo', 'scaduto', 'revocato'])) {
            // La data evento è oggi, oppure la data di scadenza se è "scaduto"
            $dataEvento = $mandato->stato === 'scaduto' ? $mandato->data_scadenza_mandato : Carbon::today()->toDateString();

            $this->registraLogAui(
                $mandato,
                'chiusura_rapporto',
                $dataEvento,
                null  // La chiusura spesso non richiede l'importo, o prende l'ultimo noto
            );
        }
    }

    private function registraLogAui(ClientMandate $mandato, string $tipoEvento, string $dataEvento, ?float $importo): void
    {
        // Controllo per evitare duplicati nella tabella di transito
        $esiste = AuiLog::where('client_mandate_id', $mandato->id)
            ->where('tipo_evento', $tipoEvento)
            ->exists();

        if (!$esiste) {
            AuiLog::create([
                'client_mandate_id' => $mandato->id,
                'pratica_id' => null,  // Non c'è ancora una pratica erogata
                'tipo_evento' => $tipoEvento,
                'data_evento' => $dataEvento,
                'importo_rilevato' => $importo,
                'stato' => 'da_consolidare',
                'payload_dati_cliente' => $mandato->cliente->toArray(),
            ]);
        }
    }
}
