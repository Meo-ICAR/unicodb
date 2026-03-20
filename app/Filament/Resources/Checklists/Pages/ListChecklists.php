<?php

namespace App\Filament\Resources\Checklists\Pages;

use App\Filament\Resources\Checklists\ChecklistResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChecklists extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = ChecklistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
