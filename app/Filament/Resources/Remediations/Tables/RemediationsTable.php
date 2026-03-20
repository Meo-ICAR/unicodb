<?php

namespace App\Filament\Resources\Remediations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RemediationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Azione di Rimedio')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('remediation_type')
                    ->label('Tipo Rimedio')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'AML' => 'Antiriciclaggio',
                        'Gestione Reclami' => 'Gestione Reclami',
                        'Monitoraggio Rete' => 'Monitoraggio Rete',
                        'Privacy' => 'Privacy',
                        'Trasparenza' => 'Trasparenza',
                        'Assetto Organizzativo' => 'Assetto Organizzativo',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'AML' => 'danger',
                        'Gestione Reclami' => 'warning',
                        'Monitoraggio Rete' => 'info',
                        'Privacy' => 'purple',
                        'Trasparenza' => 'success',
                        'Assetto Organizzativo' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('businessFunction.name')
                    ->label('Funzione Aziendale')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Non assegnata')
                    ->badge()
                    ->color('primary'),
                TextColumn::make('timeframe_formatted')
                    ->label('Tempo Richiesto')
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => $record->isUrgent() ? 'danger' : ($record->isHighPriority() ? 'warning' : 'success')),
                TextColumn::make('audit.title')
                    ->label('Audit')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->placeholder('Non assegnato'),
            ])
            ->filters([
                SelectFilter::make('remediation_type')
                    ->label('Tipo Rimedio')
                    ->options([
                        'AML' => 'Antiriciclaggio',
                        'Gestione Reclami' => 'Gestione Reclami',
                        'Monitoraggio Rete' => 'Monitoraggio Rete',
                        'Privacy' => 'Privacy',
                        'Trasparenza' => 'Trasparenza',
                        'Assetto Organizzativo' => 'Assetto Organizzativo',
                    ]),
                SelectFilter::make('business_function_id')
                    ->label('Funzione Aziendale')
                    ->relationship('businessFunction', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('audit_id')
                    ->label('Audit')
                    ->relationship('audit', 'title')
                    ->searchable()
                    ->preload(),
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
