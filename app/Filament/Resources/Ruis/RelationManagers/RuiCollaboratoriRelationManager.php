<?php

namespace App\Filament\Resources\Ruis\RelationManagers;

use App\Filament\Resources\RuiCollaboratoris\RuiCollaboratoriResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class RuiCollaboratoriRelationManager extends RelationManager
{
    protected static string $relationship = 'RuiCollaboratori';

    protected static ?string $relatedResource = RuiCollaboratoriResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
