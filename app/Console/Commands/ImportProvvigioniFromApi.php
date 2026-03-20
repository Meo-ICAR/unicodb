<?php

namespace App\Console\Commands;

use App\Services\MediafacileProvvigioniService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportProvvigioniFromApi extends Command
{
    protected $signature = 'provvigioni:import-api
                            {--start-date= : Start date (YYYY-MM-DD)}
                            {--end-date= : End date (YYYY-MM-DD)}';

    protected $description = 'Import provvigioni from external API using the Import Service';

    // Iniettiamo il service direttamente nel metodo handle()
    public function handle(MediafacileProvvigioniService $importService)
    {
        $endDate = $this->option('end-date') ? Carbon::parse($this->option('end-date')) : now();
        $startDate = $this->option('start-date')
            ? Carbon::parse($this->option('start-date'))
            : $endDate->copy()->subDays(60);

        $this->info("Inizio importazione provvigioni dal {$startDate->format('Y-m-d')} al {$endDate->format('Y-m-d')}...");

        // Chiamiamo il nostro Service
        $result = $importService->import($startDate, $endDate);

        // Gestiamo l'output per la console in base al risultato
        if (!$result['success']) {
            $this->error('Importazione fallita: ' . $result['message']);
            return Command::FAILURE;
        }

        $this->info($result['message']);

        $this->table(
            ['Creati', 'Aggiornati', 'Errori'],
            [
                [$result['imported'], $result['updated'], $result['errors']]
            ]
        );

        return Command::SUCCESS;
    }
}
