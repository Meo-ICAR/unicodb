<?php

namespace App\Filament\Resources\Comunes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ComunesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('denominazione')
                    ->label('Denominazione')
                    ->searchable(),
                TextColumn::make('sigla_automobilistica')
                    ->label('Sigla')
                    ->searchable(),
                TextColumn::make('denominazione_regione')
                    ->label('Regione')
                    ->searchable(),
                TextColumn::make('ripartizione_geografica')
                    ->label('Ripartizione'),
                TextColumn::make('codice_catastale')
                    ->label('Codice Catastale')
                    ->searchable(),
                TextColumn::make('capoluogo_provincia')
                    ->label('Capoluogo'),
                // ->boolean(),
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
