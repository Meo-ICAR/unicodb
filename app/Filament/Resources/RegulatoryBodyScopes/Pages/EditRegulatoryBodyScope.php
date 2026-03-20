<?php

namespace App\Filament\Resources\RegulatoryBodyScopes\Pages;

use App\Filament\Resources\RegulatoryBodyScopes\RegulatoryBodyScopeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRegulatoryBodyScope extends EditRecord
{
    protected static string $resource = RegulatoryBodyScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
