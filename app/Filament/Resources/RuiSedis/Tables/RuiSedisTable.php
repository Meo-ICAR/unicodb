<?php

namespace App\Filament\Resources\RuiSedis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RuiSedisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('oss')
                    ->searchable(),
                TextColumn::make('numero_iscrizione_int')
                    ->searchable(),
                TextColumn::make('tipo_sede')
                    ->searchable(),
                TextColumn::make('comune_sede')
                    ->searchable(),
                TextColumn::make('provincia_sede')
                    ->searchable(),
                TextColumn::make('cap_sede')
                    ->searchable(),
                TextColumn::make('indirizzo_sede')
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
