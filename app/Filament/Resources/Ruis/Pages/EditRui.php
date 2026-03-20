<?php

namespace App\Filament\Resources\Ruis\Pages;

use App\Filament\Resources\Ruis\RuiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRui extends EditRecord
{
    protected static string $resource = RuiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
