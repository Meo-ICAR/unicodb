<?php

namespace App\Filament\Resources\DocumentStatuses\Pages;

use App\Filament\Resources\DocumentStatuses\DocumentStatusResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDocumentStatuses extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = DocumentStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
