<?php

namespace App\Filament\Resources\Venasarcotrimestres\Pages;

use App\Filament\Resources\Venasarcotrimestres\VenasarcotrimestreResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVenasarcotrimestre extends EditRecord
{
    protected static string $resource = VenasarcotrimestreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
