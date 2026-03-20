<?php

namespace App\Filament\Resources\ApiConfigurations\Pages;

use App\Filament\Resources\ApiConfigurations\ApiConfigurationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateApiConfiguration extends CreateRecord
{
    protected static string $resource = ApiConfigurationResource::class;
}
