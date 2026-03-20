<?php

namespace App\Filament\Resources\Ruis\RelationManagers;

use App\Filament\Resources\RuiCariches\RuiCaricheResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class RuiCaricheRelationManager extends RelationManager
{
    protected static string $relationship = 'RuiCariche';

    protected static ?string $relatedResource = RuiCaricheResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
