<?php

namespace App\Filament\Resources\ChecklistDocuments\Pages;

use App\Filament\Resources\ChecklistDocuments\ChecklistDocumentResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChecklistDocuments extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = ChecklistDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
