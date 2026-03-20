<?php

namespace App\Filament\Resources\OamScopes\Pages;

use App\Filament\Resources\OamScopes\OamScopeResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOamScopes extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = OamScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
