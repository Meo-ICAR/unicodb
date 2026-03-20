<?php

namespace App\Filament\Resources\ProformaStatusHistories\Pages;

use App\Filament\Resources\ProformaStatusHistories\ProformaStatusHistoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProformaStatusHistory extends EditRecord
{
    protected static string $resource = ProformaStatusHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
