<?php

namespace App\Filament\Resources\SoftwareApplications\Pages;

use App\Filament\Resources\SoftwareApplications\SoftwareApplicationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSoftwareApplication extends EditRecord
{
    protected static string $resource = SoftwareApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
