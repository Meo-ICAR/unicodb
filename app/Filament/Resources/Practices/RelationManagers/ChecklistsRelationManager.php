<?php

namespace App\Filament\Resources\Practices\RelationManagers;

use App\Filament\RelationManagers\BaseChecklistsRelationManager;

class ChecklistsRelationManager extends BaseChecklistsRelationManager
{
    protected static ?string $title = 'Checklist Pratica';

    /**
     * Personalizzazione specifica per Practice
     */
    protected function getTargetTypeLabel(): string
    {
        return 'Pratica';
    }
}
