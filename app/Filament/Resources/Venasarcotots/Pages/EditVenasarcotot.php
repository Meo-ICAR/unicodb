<?php

namespace App\Filament\Resources\Venasarcotots\Pages;

use App\Filament\Resources\Venasarcotots\VenasarcototResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVenasarcotot extends EditRecord
{
    protected static string $resource = VenasarcototResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
