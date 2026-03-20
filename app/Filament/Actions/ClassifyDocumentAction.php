<?php

namespace App\Filament\Actions;

use App\Services\DocumentClassificationService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

class ClassifyDocumentAction extends Action
{
    public static function make(?string $name = 'classify'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Classifica Documento')
            ->icon('heroicon-o-magnifying-glass')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Classifica Documento')
            ->modalDescription('Classifica questo documento usando le regex dei tipi documento')
            ->modalSubmitActionLabel('Classifica')
            ->action(function ($record) {
                try {
                    $classificationService = new DocumentClassificationService();
                    $success = $classificationService->classifySingleDocument($record);

                    if ($success) {
                        Notification::make()
                            ->title('Documento Classificato')
                            ->body('Il documento è stato classificato con successo')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Impossibile Classificare')
                            ->body('Nessuna regex corrispondente trovata per questo documento')
                            ->warning()
                            ->send();
                    }
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Errore Classificazione')
                        ->body('Si è verificato un errore: ' . $e->getMessage())
                        ->danger()
                        ->send();

                    throw new Halt();
                }
            });
    }
}
