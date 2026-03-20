<?php

namespace App\Filament\Resources\AddressTypes\Pages;

use App\Filament\Resources\AddressTypes\AddressTypeResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAddressTypes extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = AddressTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
