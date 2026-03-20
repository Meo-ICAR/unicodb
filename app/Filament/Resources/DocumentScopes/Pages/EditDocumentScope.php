<?php

namespace App\Filament\Resources\DocumentScopes\Pages;

use App\Filament\Resources\DocumentScopes\DocumentScopeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDocumentScope extends EditRecord
{
    protected static string $resource = DocumentScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
