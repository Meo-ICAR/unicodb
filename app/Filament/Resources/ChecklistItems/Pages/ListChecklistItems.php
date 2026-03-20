<?php

namespace App\Filament\Resources\ChecklistItems\Pages;

use App\Filament\Resources\ChecklistItems\ChecklistItemResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChecklistItems extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = ChecklistItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
