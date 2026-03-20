<?php

namespace App\Filament\Resources\PrincipalEmployees\Pages;

use App\Filament\Resources\PrincipalEmployees\PrincipalEmployeeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPrincipalEmployee extends EditRecord
{
    protected static string $resource = PrincipalEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
