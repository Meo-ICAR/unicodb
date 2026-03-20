<?php

namespace App\Filament\Resources\ChecklistAudits\Pages;

use App\Filament\Resources\ChecklistAudits\ChecklistAuditResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChecklistAudits extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = ChecklistAuditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
