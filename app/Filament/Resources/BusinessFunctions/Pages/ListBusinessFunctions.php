<?php

namespace App\Filament\Resources\BusinessFunctions\Pages;

use App\Filament\Resources\BusinessFunctions\BusinessFunctionResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBusinessFunctions extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = BusinessFunctionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
