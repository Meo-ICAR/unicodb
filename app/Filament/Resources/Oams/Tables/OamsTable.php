<?php

namespace App\Filament\Resources\Oams\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('autorizzato_ad_operare')
                    ->searchable(),
                TextColumn::make('persona')
                    ->searchable(),
                TextColumn::make('codice_fiscale')
                    ->searchable(),
                TextColumn::make('domicilio_sede_legale')
                    ->searchable(),
                TextColumn::make('elenco')
                    ->searchable(),
                TextColumn::make('numero_iscrizione')
                    ->searchable(),
                TextColumn::make('data_iscrizione')
                    ->date()
                    ->sortable(),
                TextColumn::make('stato')
                    ->searchable(),
                TextColumn::make('data_stato')
                    ->date()
                    ->sortable(),
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
