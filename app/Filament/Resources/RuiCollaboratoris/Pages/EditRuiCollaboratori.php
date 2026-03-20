<?php

namespace App\Filament\Resources\RuiCollaboratoris\Pages;

use App\Filament\Resources\RuiCollaboratoris\RuiCollaboratoriResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRuiCollaboratori extends EditRecord
{
    protected static string $resource = RuiCollaboratoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
