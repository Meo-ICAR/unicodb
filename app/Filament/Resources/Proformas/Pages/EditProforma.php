<?php

namespace App\Filament\Resources\Proformas\Pages;

use App\Filament\Resources\Proformas\ProformaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProforma extends EditRecord
{
    protected static string $resource = ProformaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
