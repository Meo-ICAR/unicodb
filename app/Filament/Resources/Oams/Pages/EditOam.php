<?php

namespace App\Filament\Resources\Oams\Pages;

use App\Filament\Resources\Oams\OamResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOam extends EditRecord
{
    protected static string $resource = OamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
