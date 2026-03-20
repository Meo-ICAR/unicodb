<?php

namespace App\Filament\Resources\AddressTypes\Pages;

use App\Filament\Resources\AddressTypes\AddressTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAddressType extends CreateRecord
{
    protected static string $resource = AddressTypeResource::class;
}
