<?php

namespace App\Filament\Resources\AuiRecords\Pages;

use App\Filament\Resources\AuiRecords\AuiRecordResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAuiRecord extends ViewRecord
{
    protected static string $resource = AuiRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
