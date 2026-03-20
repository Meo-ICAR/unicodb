<?php

namespace App\Filament\Resources\DocumentTypeScopes\Pages;

use App\Filament\Resources\DocumentTypeScopes\DocumentTypeScopeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDocumentTypeScope extends EditRecord
{
    protected static string $resource = DocumentTypeScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
