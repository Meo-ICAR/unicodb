<?php

namespace App\Filament\Resources\AuiRecords\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AuiRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('activityLog.id')
                    ->searchable(),
                TextColumn::make('practice.name')
                    ->searchable(),
                TextColumn::make('client.name')
                    ->searchable(),
                TextColumn::make('codice_univoco_aui')
                    ->searchable(),
                TextColumn::make('tipo_registrazione')
                    ->searchable(),
                TextColumn::make('data_registrazione')
                    ->date()
                    ->sortable(),
                TextColumn::make('importo_operazione')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('profilo_rischio')
                    ->searchable(),
                IconColumn::make('is_annullato')
                    ->boolean(),
                TextColumn::make('motivo_annullamento')
                    ->searchable(),
                TextColumn::make('company.name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
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
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
