<?php

namespace App\Filament\Resources\RaciAssignements\Pages;

use App\Filament\Resources\RaciAssignements\RaciAssignementResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRaciAssignements extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = RaciAssignementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
