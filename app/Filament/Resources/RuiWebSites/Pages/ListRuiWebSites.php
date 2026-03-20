<?php

namespace App\Filament\Resources\RuiWebSites\Pages;

use App\Filament\Resources\RuiWebSites\RuiWebSitesResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuiWebSites extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = RuiWebSitesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
