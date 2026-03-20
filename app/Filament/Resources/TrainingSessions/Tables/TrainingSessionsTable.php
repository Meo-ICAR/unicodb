<?php

namespace App\Filament\Resources\TrainingSessions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainingSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with('trainingTemplate'))
            ->columns([
                TextColumn::make('trainingTemplate.name')
                    ->label('Template Corso')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nome Sessione')
                    ->searchable(),
                TextColumn::make('total_hours')
                    ->label('Ore Totali')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('trainer_name')
                    ->label('Docente')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Data Inizio')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Data Fine')
                    ->date()
                    ->sortable(),
                TextColumn::make('location')
                    ->label('Location')
                    ->badge(),
                TextColumn::make('updated_at')
                    ->label('Data Aggiornamento')
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
