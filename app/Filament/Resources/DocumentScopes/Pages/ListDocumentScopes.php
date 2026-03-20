<?php

namespace App\Filament\Resources\DocumentScopes\Pages;

use App\Filament\Resources\DocumentScopes\DocumentScopeResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDocumentScopes extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = DocumentScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
