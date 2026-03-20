<?php

namespace App\Filament\Resources\CompanySenders\Pages;

use App\Filament\Resources\CompanySenders\CompanySenderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompanySender extends EditRecord
{
    protected static string $resource = CompanySenderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
