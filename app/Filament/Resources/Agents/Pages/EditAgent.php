<?php

namespace App\Filament\Resources\Agents\Pages;

use App\Filament\Resources\Agents\AgentResource;
use App\Models\Agent;
use App\Services\ChecklistService;
use App\Services\GeminiVisionService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAgent extends EditRecord
{
    protected static string $resource = AgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('estrai_dati_ocr')
                ->disabled(fn() => !$this->record->hasMedia('identity_documents'))
                ->label('Estrai Dati dai Documenti (IA)')
                ->icon('heroicon-o-sparkles')  // Un'icona per indicare l'AI
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Estrai dati dal documento')
                ->modalDescription('Vuoi usare Gemini per leggere il documento e aggiornare i campi di questo agente?')
                ->action(function (Agent $record, GeminiVisionService $geminiService) {
                    // 1. Recuperiamo il path fisico del file da Spatie Media Library
                    $imagePath = $record->getFirstMediaPath('identity_documents');

                    if (!file_exists($imagePath)) {
                        Notification::make()
                            ->title('Errore')
                            ->body('Nessun documento fisico trovato sul server.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // 2. Chiamiamo il nostro Service Gemini
                    $extractedData = $geminiService->extractIdentityData($imagePath);

                    if (!$extractedData) {
                        Notification::make()
                            ->title('Estrazione Fallita')
                            ->body("Impossibile leggere il documento d'identità.")
                            ->danger()
                            ->send();
                        return;
                    }

                    // 3. Aggiorniamo il record nel database con i dati estratti
                    $record->update([
                        'nome' => $extractedData['nome'] ?? $record->nome,
                        'cognome' => $extractedData['cognome'] ?? $record->cognome,
                        'numero_documento' => $extractedData['numero_documento'] ?? $record->numero_documento,
                    ]);

                    // 4. Mostriamo una notifica di successo in stile Filament
                    Notification::make()
                        ->title('Dati Estratti con Successo!')
                        ->success()
                        ->send();
                }),
            Action::make('assegnaChecklistOnBoarding')
                ->label('Candidatura')
                ->icon('heroicon-o-clipboard-document-check')
                ->disabled(fn() => empty($record->oam))
                ->action(function (Agent $record, ChecklistService $checklistService) {
                    try {
                        // Chiamiamo il nostro Service pulitissimo
                        $checklistService->assignTemplate($record, 'ONBOARDING_AGENT');

                        Notification::make()
                            ->success()
                            ->title('Checklist Assegnata!')
                            ->body("La procedura Candidatura è pronta per essere compilata nel fascicolo dell'agente.")
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Errore')
                            ->body('Template checklist non trovato.')
                            ->send();
                    }
                }),
            Action::make('assegnaChecklistOam')
                ->disabled(fn() => !empty($record->oam) && !empty($record->dismissed_at))
                ->label('Comunicazione OAM Avvio / Cessazione')
                ->icon('heroicon-o-clipboard-document-check')
                ->action(function (Agent $record, ChecklistService $checklistService) {
                    try {
                        // Chiamiamo il nostro Service pulitissimo
                        $checklistService->assignTemplate($record, 'OAM_RETE_10GG');

                        Notification::make()
                            ->success()
                            ->title('Checklist Assegnata!')
                            ->body("La procedura OAM è pronta per essere compilata nel fascicolo dell'agente.")
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Errore')
                            ->body('Template checklist non trovato.')
                            ->send();
                    }
                }),
            DeleteAction::make(),
        ];
    }
}
