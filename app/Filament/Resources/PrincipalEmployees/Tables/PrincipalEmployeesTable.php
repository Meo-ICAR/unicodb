<?php

namespace App\Filament\Resources\PrincipalEmployees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PrincipalEmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('principal.name')
                    ->label('Banca/Principal')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('usercode')
                    ->label('Codice Utente')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Codice copiato!'),
                TextColumn::make('description')
                    ->label('Descrizione')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('start_date')
                    ->label('Inizio')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Fine')
                    ->date()
                    ->sortable()
                    ->placeholder('Indeterminato'),
                IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('is_currently_active')
                    ->label('Stato Corrente')
                    ->getStateUsing(fn($record) => $record->is_currently_active ? 'Attivo' : 'Non Attivo')
                    ->badge()
                    ->color(fn($record) => $record->is_currently_active ? 'success' : 'danger'),
            ])
            ->filters([
                SelectFilter::make('principal')
                    ->relationship('principal', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('is_active')
                    ->label('Stato')
                    ->options([
                        '1' => 'Attivo',
                        '0' => 'Inattivo',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
