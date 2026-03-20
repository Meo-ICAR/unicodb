<?php

namespace App\Filament\Resources\RuiSezds\Pages;

use App\Filament\Resources\RuiSezds\RuiSezdsResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuiSezds extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = RuiSezdsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
