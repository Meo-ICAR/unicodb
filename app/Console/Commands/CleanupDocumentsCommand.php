<?php

namespace App\Console\Commands;

use App\Services\DocumentClassificationService;
use Illuminate\Console\Command;

class CleanupDocumentsCommand extends Command
{
    // Questa è la stringa che scrivi nel terminale
    protected $signature = 'documents:classify-existing {--limit= : Limite di documenti da processare} {--verbose : Mostra risultati dettagliati}';

    protected $description = 'Classifica i documenti esistenti usando le regex della tabella document_types';

    public function handle()
    {
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $verbose = $this->option('verbose');

        $this->info('Inizio classificazione documenti...');

        $classificationService = new DocumentClassificationService();
        $results = $classificationService->classifyDocuments($limit, $verbose);

        // Mostra statistiche iniziali
        $stats = $classificationService->getClassificationStats();
        $this->info('Statistiche attuali:');
        $this->line("  - Documenti totali: {$stats['total_documents']}");
        $this->line("  - Documenti classificati: {$stats['classified_documents']}");
        $this->line("  - Documenti non classificati: {$stats['unclassified_documents']}");
        $this->line("  - Percentuale classificazione: {$stats['classification_percentage']}%");
        $this->line("  - Tipi con regex: {$stats['types_with_regex']}");
        $this->newLine();

        // Mostra errori se presenti
        if (!empty($results['errors'])) {
            $this->error('Errori durante la classificazione:');
            foreach ($results['errors'] as $error) {
                $this->error("  - {$error}");
            }
            return 1;
        }

        // Mostra risultati
        $this->info('Risultati classificazione:');
        $this->line("  - Documenti processati: {$results['processed_documents']}");
        $this->line("  - Documenti classificati: {$results['classified_documents']}");
        $this->line("  - Documenti non classificati: {$results['unclassified_documents']}");

        // Mostra dettagli se verbose
        if ($verbose && !empty($results['details'])) {
            $this->newLine();
            $this->info('Dettagli elaborazione:');

            foreach ($results['details'] as $detail) {
                $status = $detail['classified'] ? '✓' : '✗';
                $typeInfo = $detail['matched_type']
                    ? " ({$detail['matched_type']['name']})"
                    : '';
                $errorInfo = $detail['error'] ? " - ERRORE: {$detail['error']}" : '';

                $this->line("  {$status} ID:{$detail['document_id']} - {$detail['document_name']}{$typeInfo}{$errorInfo}");
            }
        }

        $this->newLine();
        $this->info('Classificazione completata!');

        return 0;
    }
}
