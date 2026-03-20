<?php

namespace App\Filament\Resources\Principals\RelationManagers;

use App\Filament\RelationManagers\BaseChecklistsRelationManager;

class ChecklistsRelationManager extends BaseChecklistsRelationManager
{
    protected static ?string $title = 'Checklist Mandante';

    /**
     * Personalizzazione specifica per Principal
     */
    protected function getTargetTypeLabel(): string
    {
        return 'Mandante';
    }
}
