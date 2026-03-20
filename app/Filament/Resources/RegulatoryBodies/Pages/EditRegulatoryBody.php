<?php

namespace App\Filament\Resources\RegulatoryBodies\Pages;

use App\Filament\Resources\RegulatoryBodies\RegulatoryBodyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRegulatoryBody extends EditRecord
{
    protected static string $resource = RegulatoryBodyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
