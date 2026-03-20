<?php

namespace App\Filament\Resources\Addresses\Pages;

use App\Filament\Resources\Addresses\AddressResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAddresses extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = AddressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
