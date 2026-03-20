<?php

namespace App\Filament\Resources\ProformaStatuses\Pages;

use App\Filament\Resources\ProformaStatuses\ProformaStatusResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProformaStatus extends EditRecord
{
    protected static string $resource = ProformaStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
