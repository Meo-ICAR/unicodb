<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use App\Filament\RelationManagers\BaseChecklistsRelationManager;

class ChecklistsRelationManager extends BaseChecklistsRelationManager
{
    protected static ?string $title = 'Checklist Cliente';

    /**
     * Personalizzazione specifica per Client
     */
    protected function getTargetTypeLabel(): string
    {
        return 'Cliente';
    }
}
