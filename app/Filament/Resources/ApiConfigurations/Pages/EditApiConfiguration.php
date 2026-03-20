<?php

namespace App\Filament\Resources\ApiConfigurations\Pages;

use App\Filament\Resources\ApiConfigurations\ApiConfigurationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditApiConfiguration extends EditRecord
{
    protected static string $resource = ApiConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
