<?php

namespace App\Filament\Resources\PracticeCommissions\Tables;

use App\Filament\Imports\PracticeCommissionsImporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Maatwebsite\Excel\Excel;

class PracticeCommissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('agent.name')
                    ->label('Agente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Importo')
                    ->money('EUR')  // Forza Euro e formato italiano
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('perfected_at')
                    ->label('Perfezionata')
                    ->date()
                    ->sortable(),
                TextColumn::make('practice.name')
                    ->label('Practica')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('proforma.name')
                    ->label('Proforma')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('practiceCommissionStatus.status_payment')
                    ->label('Stato Commissione')
                    ->badge()
                    ->color(fn($record) => $record->practiceCommissionStatus?->is_perfectioned
                        ? 'success'
                        : ($record->practiceCommissionStatus?->is_working ? 'warning' : 'gray'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessuno stato'),
                TextColumn::make('principal.name')
                    ->label('Mandante')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Descrizione')
                    ->searchable(),
                IconColumn::make('is_coordination')
                    ->label('Coord.')
                    ->boolean(),
                TextColumn::make('cancellation_at')
                    ->label('Annullata')
                    ->date()
                    ->sortable(),
                TextColumn::make('invoice_number')
                    ->label('Num. fattura')
                    ->searchable(),
                TextColumn::make('invoice_at')
                    ->label('Fattura del')
                    ->date()
                    ->sortable(),
                TextColumn::make('paided_at')
                    ->label('Pagata il')
                    ->date()
                    ->sortable(),
                IconColumn::make('is_storno')
                    ->label('Storno')
                    ->boolean(),
                IconColumn::make('is_enasarco')
                    ->label('Enasarco')
                    ->boolean(),
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
                ImportAction::make('import')
                    ->label('Importa Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->importer(PracticeCommissionsImporter::class)
                    ->maxRows(1000),
            ]);
    }
}
