<?php

namespace App\Filament\Resources\Venasarcotots\Pages;

use App\Filament\Resources\Venasarcotots\VenasarcototResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVenasarcotots extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = VenasarcototResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
