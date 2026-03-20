<?php

namespace App\Filament\Actions;

use App\Services\DocumentClassificationService;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class BulkClassifyDocumentsAction extends BulkAction
{
    public static function make(?string $name = 'bulk_classify'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Classifica Selezionati')
            ->icon('heroicon-o-magnifying-glass')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Classifica Documenti Selezionati')
            ->modalDescription('Classifica tutti i documenti selezionati usando le regex dei tipi documento')
            ->modalSubmitActionLabel('Classifica Tutti')
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection $records) {
                try {
                    $classificationService = new DocumentClassificationService();
                    $classified = 0;
                    $unclassified = 0;
                    $errors = 0;

                    foreach ($records as $document) {
                        try {
                            $success = $classificationService->classifySingleDocument($document);

                            if ($success) {
                                $classified++;
                            } else {
                                $unclassified++;
                            }
                        } catch (\Exception $e) {
                            $errors++;
                        }
                    }

                    $message = "Classificazione completata!\n"
                        . "Classificati: {$classified}\n"
                        . "Non classificati: {$unclassified}"
                        . ($errors > 0 ? "\nErrori: {$errors}" : '');

                    Notification::make()
                        ->title('Classificazione Bulk Completata')
                        ->body($message)
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Errore Classificazione Bulk')
                        ->body('Si è verificato un errore: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->visible(fn(Collection $records) => $records->some(fn($record) => $record->document_type_id === null));
    }
}
