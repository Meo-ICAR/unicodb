<?php

namespace App\Filament\Resources\Vcoges\Pages;

use App\Filament\Resources\Vcoges\VcogesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVcoges extends EditRecord
{
    protected static string $resource = VcogesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
