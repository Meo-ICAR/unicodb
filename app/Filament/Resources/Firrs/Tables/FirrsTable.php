<?php

namespace App\Filament\Resources\Firrs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FirrsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('minimo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('massimo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('aliquota')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('competenza')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('enasarco')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ]);
    }
}
