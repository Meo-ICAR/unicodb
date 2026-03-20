<?php

namespace App\Filament\Resources\PrincipalMandates\Pages;

use App\Filament\Resources\PrincipalMandates\PrincipalMandateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrincipalMandate extends EditRecord
{
    protected static string $resource = PrincipalMandateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
