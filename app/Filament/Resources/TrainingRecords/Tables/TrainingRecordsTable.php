<?php

namespace App\Filament\Resources\TrainingRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainingRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['trainable', 'trainingSession']))
            ->columns([
                TextColumn::make('trainingSession.title')
                    ->label('Sessione Formativa')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                TextColumn::make('trainable_type')
                    ->label('Tipo Partecipante')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'App\Models\Employee' => 'Dipendente',
                        'App\Models\Agent' => 'Agente',
                        'App\Models\Company' => 'Azienda',
                        'App\Models\Client' => 'Cliente',
                        default => $state,
                    })
                    ->badge()
                    ->sortable(),
                TextColumn::make('trainable.name')
                    ->label('Partecipante')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn($record) => $record->trainable ? $record->trainable->name : null),
                TextColumn::make('status')
                    ->label('Stato')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'ISCRITTO' => 'Iscritto',
                        'FREQUENTANTE' => 'Frequentante',
                        'COMPLETATO' => 'Completato',
                        'NON_SUPERATO' => 'Non Superato',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'ISCRITTO' => 'info',
                        'FREQUENTANTE' => 'warning',
                        'COMPLETATO' => 'success',
                        'NON_SUPERATO' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('name')
                    ->label('Descrizione')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('hours_attended')
                    ->label('Ore')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('score')
                    ->label('Esito')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('completion_date')
                    ->label('Completamento')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('certificate_path')
                    ->label('Certificato')
                    ->searchable()
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
