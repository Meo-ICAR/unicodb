<?php

namespace App\Filament\Resources\AuiRecords\Pages;

use App\Filament\Resources\AuiRecords\AuiRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAuiRecord extends EditRecord
{
    protected static string $resource = AuiRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
