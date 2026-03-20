<?php

namespace App\Filament\Resources\Ruis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RuisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ragione_sociale')
                    ->searchable(),
                TextColumn::make('cognome_nome')
                    ->searchable(),
                TextColumn::make('numero_iscrizione_rui')
                    ->searchable(),
                TextColumn::make('data_iscrizione')
                    ->date()
                    ->sortable(),
                TextColumn::make('oss')
                    ->searchable(),
                IconColumn::make('inoperativo')
                    ->boolean(),
                TextColumn::make('data_inizio_inoperativita')
                    ->date()
                    ->sortable(),
                TextColumn::make('stato')
                    ->searchable(),
                TextColumn::make('comune_nascita')
                    ->searchable(),
                TextColumn::make('data_nascita')
                    ->date()
                    ->sortable(),
                TextColumn::make('provincia_nascita')
                    ->searchable(),
                TextColumn::make('titolo_individuale_sez_a')
                    ->searchable(),
                TextColumn::make('attivita_esercitata_sez_a')
                    ->searchable(),
                TextColumn::make('titolo_individuale_sez_b')
                    ->searchable(),
                TextColumn::make('attivita_esercitata_sez_b')
                    ->searchable(),
                TextColumn::make('rui_section_id')
                    ->numeric()
                    ->sortable(),
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
