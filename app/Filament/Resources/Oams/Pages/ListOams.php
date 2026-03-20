<?php

namespace App\Filament\Resources\Oams\Pages;

use App\Filament\Resources\Oams\OamResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOams extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = OamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
