<?php

namespace App\Filament\Resources\RuiAccessoris\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RuiAccessorisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('numero_iscrizione_e')
                    ->searchable(),
                TextColumn::make('ragione_sociale')
                    ->searchable(),
                TextColumn::make('cognome_nome')
                    ->searchable(),
                TextColumn::make('sede_legale')
                    ->searchable(),
                TextColumn::make('data_nascita')
                    ->date()
                    ->sortable(),
                TextColumn::make('luogo_nascita')
                    ->searchable(),
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
