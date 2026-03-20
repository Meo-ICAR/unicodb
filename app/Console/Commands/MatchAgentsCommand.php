<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Services\OamMatcherService;
use Illuminate\Console\Command;

class MatchAgentsCommand extends Command
{
    // Il nome del comando da lanciare nel terminale
    protected $signature = 'agent:match-oam {--force : Sovrascrive anche i CF presenti}';
    protected $description = 'Abbina i nomi agenti di agents con la tabella ufficiale OAM';

    public function handle(AgentMatcherService $matcher)
    {
        $this->info('🚀 Avvio scansione completa della tabella Agent...');

        // Contatori per il riepilogo finale
        $countMatch = 0;
        $countFail = 0;

        // Usiamo chunk per gestire grandi volumi di dati senza saturare la RAM
        Agent::whereNotNull('abi')->chunk(100, function ($Agents) use ($matcher, &$countMatch, &$countFail) {
            foreach ($Agents as $p) {
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
