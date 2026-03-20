<?php

namespace App\Filament\Resources\DocumentTypeScopes\Pages;

use App\Filament\Resources\DocumentTypeScopes\DocumentTypeScopeResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDocumentTypeScopes extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = DocumentTypeScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
