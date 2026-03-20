<?php

namespace App\Filament\Resources\RaciAssignements\Pages;

use App\Filament\Resources\RaciAssignements\RaciAssignementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRaciAssignement extends EditRecord
{
    protected static string $resource = RaciAssignementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
