<?php

namespace App\Filament\Resources\PrincipalScopes\Pages;

use App\Filament\Resources\PrincipalScopes\PrincipalScopeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPrincipalScope extends EditRecord
{
    protected static string $resource = PrincipalScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
