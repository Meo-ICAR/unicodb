<?php

namespace App\Filament\Resources\ApiConfigurations\Pages;

use App\Filament\Resources\ApiConfigurations\ApiConfigurationResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApiConfigurations extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = ApiConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
