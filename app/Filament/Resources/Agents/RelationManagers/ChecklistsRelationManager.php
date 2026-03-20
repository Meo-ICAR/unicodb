<?php

namespace App\Filament\Resources\Agents\RelationManagers;

use App\Filament\RelationManagers\BaseChecklistsRelationManager;

class ChecklistsRelationManager extends BaseChecklistsRelationManager
{
    protected static ?string $title = 'Checklist Agente';

    /**
     * Personalizzazione specifica per Agent
     */
    protected function getTargetTypeLabel(): string
    {
        return 'Agente';
    }
}
