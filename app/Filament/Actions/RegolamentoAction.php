<?php

namespace App\Filament\Actions;

use App\Models\Document;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;

class RegolamentoAction extends Action
{
    public static function make(string $name = 'regolamento'): static
    {
        return parent::make($name)
            ->label('Regolamento')
            ->icon('heroicon-o-document-text')
            ->color('info')
            ->url(function (Action $action) {
                // Ottieni il modello dalla risorsa corrente
                $resource = $action->getLivewire()->getResource();
                if (!$resource) {
                    return null;
                }

                $modelClass = $resource::getModel();
                $modelName = class_basename($modelClass);

                // Cerca un documento con model uguale ma senza documentable_id
                $document = Document::where('documentable_type', $modelClass)
                    ->whereNull('documentable_id')
                    ->first();

                if ($document) {
                    // Se il documento ha un URL, reindirizza lì
                    if ($document->url_document) {
                        return $document->url_document;
                    }

                    // Se il documento ha media, reindirizza al primo media
                    if ($document->hasMedia()) {
                        $media = $document->getFirstMedia();
                        return $media->getUrl();
                    }

                    // Altrimenti reindirizza alla pagina di edit del documento
                    return route('filament.admin.resources.documents.edit', ['record' => $document->id]);
                }

                // Nessuna notifica, restituisci null se non trovato
                return null;
            })
            ->openUrlInNewTab()
            ->disabled(function (Action $action) {
                // Ottieni il modello dalla risorsa corrente
                $resource = $action->getLivewire()->getResource();
                if (!$resource) {
                    return true;  // Disabilita se non c'è risorsa
                }

                $modelClass = $resource::getModel();

                // Disabilita se non esiste un documento di regolamento
                return !Document::where('documentable_type', $modelClass)
                    ->whereNull('documentable_id')
                    ->exists();
            })
            ->visible(function (Action $action) {
                // Rendi sempre visibile ma disabilitato se necessario
                return true;
            });
    }
}
