<?php

namespace App\Filament\Resources\OAMSogettis\Pages;

use App\Filament\Resources\OAMSogettis\OAMSogettiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOAMSogettis extends ListRecords
{
    protected static string $resource = OAMSogettiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
