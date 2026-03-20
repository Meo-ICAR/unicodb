<?php

namespace App\Filament\Resources\RegulatoryBodyScopes\Pages;

use App\Filament\Resources\RegulatoryBodyScopes\RegulatoryBodyScopeResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRegulatoryBodyScopes extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = RegulatoryBodyScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
