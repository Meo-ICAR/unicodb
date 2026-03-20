<?php

namespace App\Filament\Resources\Firrs\Pages;

use App\Filament\Resources\Firrs\FirrResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFirr extends EditRecord
{
    protected static string $resource = FirrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
