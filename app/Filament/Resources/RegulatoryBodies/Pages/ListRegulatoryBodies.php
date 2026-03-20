<?php

namespace App\Filament\Resources\RegulatoryBodies\Pages;

use App\Filament\Resources\RegulatoryBodies\RegulatoryBodyResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRegulatoryBodies extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = RegulatoryBodyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
