<?php

namespace App\Filament\Resources\SoftwareMappings\Pages;

use App\Filament\Resources\SoftwareMappings\SoftwareMappingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSoftwareMapping extends EditRecord
{
    protected static string $resource = SoftwareMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
