<?php

namespace App\Filament\Resources\CompanySenders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompanySendersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with('company'))
            ->columns([
                TextColumn::make('name')
                    ->label('Nome Sender')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email Sender')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('eventgroup')
                    ->label('Gruppo Evento')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessun gruppo'),
                TextColumn::make('eventcode')
                    ->label('Codice Evento')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessun codice'),
                TextColumn::make('emails')
                    ->label('Email CC')
                    ->limit(30)
                    ->tooltip(fn($record): string => $record->emails ?? 'Nessuna email CC')
                    ->placeholder('Nessuna email CC'),
                IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean(),
                TextColumn::make('company.name')
                    ->label('Azienda')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessuna azienda'),
                TextColumn::make('updated_at')
                    ->label('Aggiornamento')
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
