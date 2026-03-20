<?php

namespace App\Filament\Resources\PrincipalContacts\Pages;

use App\Filament\Resources\PrincipalContacts\PrincipalContactResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPrincipalContact extends EditRecord
{
    protected static string $resource = PrincipalContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
