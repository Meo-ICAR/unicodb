<?php

namespace App\Filament\Resources\ChecklistAudits\Pages;

use App\Filament\Resources\ChecklistAudits\ChecklistAuditResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditChecklistAudit extends EditRecord
{
    protected static string $resource = ChecklistAuditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
