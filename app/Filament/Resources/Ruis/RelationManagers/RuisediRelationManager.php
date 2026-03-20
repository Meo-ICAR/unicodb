<?php

namespace App\Filament\Resources\Ruis\RelationManagers;

use App\Filament\Resources\RuiSedis\RuiSediResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class RuisediRelationManager extends RelationManager
{
    protected static string $relationship = 'ruisedi';

    protected static ?string $relatedResource = RuiSediResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
