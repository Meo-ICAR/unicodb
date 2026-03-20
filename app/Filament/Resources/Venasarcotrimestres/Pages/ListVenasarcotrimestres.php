<?php

namespace App\Filament\Resources\Venasarcotrimestres\Pages;

use App\Filament\Resources\Venasarcotrimestres\VenasarcotrimestreResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVenasarcotrimestres extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = VenasarcotrimestreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
