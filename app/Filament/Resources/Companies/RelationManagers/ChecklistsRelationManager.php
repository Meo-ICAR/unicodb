<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\RelationManagers\BaseChecklistsRelationManager;

class ChecklistsRelationManager extends BaseChecklistsRelationManager
{
    protected static ?string $title = 'Checklist Azienda';

    /**
     * Personalizzazione specifica per Company
     */
    protected function getTargetTypeLabel(): string
    {
        return 'Azienda';
    }
}
