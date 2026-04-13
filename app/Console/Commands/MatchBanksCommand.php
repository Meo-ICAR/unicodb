<?php

namespace App\Console\Commands;

use App\Models\Principal;
use App\Services\BankMatcherService;
use Illuminate\Console\Command;

class MatchBanksCommand extends Command
{
    // Il nome del comando da lanciare nel terminale
    protected $signature = 'banks:match-abi {--force : Sovrascrive anche gli ABI già presenti}';
    protected $description = 'Abbina i nomi banche di Principal con la tabella ufficiale ABI';

    /**
     * Esegue il comando di abbinamento ABI per tutti i Principal.
     *
     * Flusso di esecuzione:
     * 1. Itera in chunk da 100 record sulla tabella `principals` (solo quelli con ABI non nullo).
     * 2. Per ogni Principal chiama `BankMatcherService::findBestAbi()` passando il nome della banca.
     * 3. Se viene trovato un match, aggiorna il campo `abi` del record con il codice a 5 cifre.
     * 4. Al termine mostra un riepilogo con il numero di match effettuati e di fallimenti.
     *
     * @param  BankMatcherService  $matcher  Servizio di matching fuzzy nome banca → codice ABI.
     * @return int
     */
    public function handle(BankMatcherService $matcher)
    {
        $this->info('🚀 Avvio scansione completa della tabella Principal...');

        // Contatori per il riepilogo finale
        $countMatch = 0;
        $countFail = 0;

        // Usiamo chunk per gestire grandi volumi di dati senza saturare la RAM
        Principal::whereNotNull('abi')->chunk(100, function ($principals) use ($matcher, &$countMatch, &$countFail) {
            foreach ($principals as $p) {
                // Cerchiamo l'ABI tramite il Service
                $foundAbi = $matcher->findBestAbi($p->name);

                if ($foundAbi) {
                    $oldAbi = $p->abi ?? 'NULL';
                    $newAbi = str_pad($foundAbi, 5, '0', STR_PAD_LEFT);

                    // Aggiorniamo il record
                    $p->abi = $newAbi;
                    $p->save();

                    // Mostra il match nel terminale (VERDE)
                    if ($newAbi <> $oldAbi) {
                        $this->line("<info>[MATCH]</info> Nome: <comment>{$p->name}</comment> ➔ ABI assegnato: <info>{$newAbi}</info> (era: {$oldAbi})");
                    }
                    $countMatch++;
                } else {
                    // Mostra il fallimento nel terminale (ROSSO)
                    $this->line("<fg=red>[FALLITO]</> Nessun match trovato per: <comment>{$p->name}</comment>");
                    $countFail++;
                }
            }
        });

        $this->newLine();
        $this->info('---------------------------------------');
        $this->info('✅ Elaborazione completata!');
        $this->info("📈 Match effettuati: $countMatch");
        $this->error("❌ Match falliti: $countFail");
        $this->info('---------------------------------------');
    }
}
