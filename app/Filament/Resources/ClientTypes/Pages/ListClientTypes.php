<?php

namespace App\Filament\Resources\ClientTypes\Pages;

use App\Filament\Resources\ClientTypes\ClientTypeResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientTypes extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = ClientTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
