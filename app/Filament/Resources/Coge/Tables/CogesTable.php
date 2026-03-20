<?php

namespace App\Filament\Resources\Coge\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CogesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->searchable(),
                TextColumn::make('fonte')
                    ->searchable(),
                TextColumn::make('entrata_uscita')
                    ->searchable(),
                TextColumn::make('conto_avere')
                    ->searchable(),
                TextColumn::make('descrizione_avere')
                    ->searchable(),
                TextColumn::make('conto_dare')
                    ->searchable(),
                TextColumn::make('descrizione_dare')
                    ->searchable(),
                TextColumn::make('annotazioni')
                    ->searchable(),
                TextColumn::make('value_type')
                    ->searchable(),
                TextColumn::make('value_period')
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
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
