<?php

namespace App\Filament\Resources\OAMSogettis\Pages;

use App\Filament\Resources\OAMSogettis\OAMSogettiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOAMSogetti extends EditRecord
{
    protected static string $resource = OAMSogettiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
