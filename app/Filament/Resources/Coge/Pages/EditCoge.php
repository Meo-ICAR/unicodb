<?php

namespace App\Filament\Resources\Coge\Pages;

use App\Filament\Resources\Coge\CogeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCoge extends EditRecord
{
    protected static string $resource = CogeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
