<?php

namespace App\Filament\Resources\ClientTypes\Pages;

use App\Filament\Resources\ClientTypes\ClientTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClientType extends CreateRecord
{
    protected static string $resource = ClientTypeResource::class;
}
