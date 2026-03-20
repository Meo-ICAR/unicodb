<?php

namespace App\Filament\Resources\RuiMandatis\Pages;

use App\Filament\Resources\RuiMandatis\RuiMandatiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRuiMandati extends EditRecord
{
    protected static string $resource = RuiMandatiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
