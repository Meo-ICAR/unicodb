<?php

namespace App\Filament\Resources\RuiAccessoris\Pages;

use App\Filament\Resources\RuiAccessoris\RuiAccessorisResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuiAccessoris extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = RuiAccessorisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
