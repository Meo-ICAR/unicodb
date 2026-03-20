<?php

namespace App\Filament\Resources\ClientTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label('Nome Tipo Cliente'),
                IconColumn::make('is_person')
                    ->label('Persona Fisica')
                    ->boolean(),
                IconColumn::make('is_company')
                    ->label('Società')
                    ->boolean(),
                TextColumn::make('privacy_role')
                    ->label('Ruolo Privacy')
                    ->searchable()
                    ->placeholder('Non configurato')
                    ->toggleable(),
                TextColumn::make('retention_period')
                    ->label('Conservazione')
                    ->searchable()
                    ->placeholder('Non specificato')
                    ->toggleable(),
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
