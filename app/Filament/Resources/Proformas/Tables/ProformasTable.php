<?php

namespace App\Filament\Resources\Proformas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Maatwebsite\Excel\Excel;

class ProformasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('agent.name')
                    ->label('Agente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Proforma')
                    ->searchable(),
                TextColumn::make('total_commissions')
                    ->label('Provvigioni')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('net_amount')
                    ->label('Netto')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('enasarco_retained')
                    ->label('Enasarco Trattenuto')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('commission_label')
                    ->label('Provvigione')
                    ->searchable(),
                TextColumn::make('remburse')
                    ->label('Rimborso')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remburse_label')
                    ->label('Etichetta Rimborso')
                    ->searchable(),
                TextColumn::make('contribute')
                    ->label('Contributo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('contribute_label')
                    ->label('Etichetta Contributo')
                    ->searchable(),
                TextColumn::make('refuse')
                    ->label('Storno')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('refuse_label')
                    ->label('Etichetta Storno')
                    ->searchable(),
                TextColumn::make('month')
                    ->label('Mese')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('year')
                    ->label('Anno')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Stato')
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
                \Filament\Actions\ImportAction::make('import')
                    ->label('Importa Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->importer(\App\Filament\Imports\ProformasImporter::class)
                    ->maxRows(1000),
            ]);
    }
}
