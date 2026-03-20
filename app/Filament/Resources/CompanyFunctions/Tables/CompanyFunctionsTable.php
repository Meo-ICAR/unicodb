<?php

namespace App\Filament\Resources\CompanyFunctions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CompanyFunctionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('function.name')
                    ->label('Funzione')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('internalEmployee.name')
                    ->label('Referente Interno')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Non assegnato'),
                TextColumn::make('externalClient.name')
                    ->label('Referente Esterno')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Non assegnato'),
                IconColumn::make('is_privacy')
                    ->label('Privacy')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->sortable(),
                IconColumn::make('is_outsourced')
                    ->label('Esternalizzata')
                    ->boolean()
                    ->trueIcon('heroicon-o-building-office-2')
                    ->falseIcon('heroicon-o-building-office')
                    ->sortable(),
                TextColumn::make('contract_status')
                    ->label('Stato Contratto')
                    ->badge()
                    ->color(fn($record) => $record->contract_status_color)
                    ->sortable(),
                TextColumn::make('report_frequency')
                    ->label('Frequenza Report')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Non specificata'),
            ])
            ->filters([
                SelectFilter::make('function_id')
                    ->label('Funzione')
                    ->relationship('function', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('is_privacy')
                    ->label('Privacy')
                    ->options([
                        '1' => 'Sì',
                        '0' => 'No',
                    ]),
                SelectFilter::make('is_outsourced')
                    ->label('Stato')
                    ->options([
                        '0' => 'Interno',
                        '1' => 'Esternalizzato',
                    ]),
                SelectFilter::make('internal_employee_id')
                    ->label('Referente Interno')
                    ->relationship('internalEmployee', 'name')
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
