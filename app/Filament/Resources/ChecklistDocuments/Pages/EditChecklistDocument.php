<?php

namespace App\Filament\Resources\ChecklistDocuments\Pages;

use App\Filament\Resources\ChecklistDocuments\ChecklistDocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditChecklistDocument extends EditRecord
{
    protected static string $resource = ChecklistDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
