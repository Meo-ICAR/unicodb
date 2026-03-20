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
