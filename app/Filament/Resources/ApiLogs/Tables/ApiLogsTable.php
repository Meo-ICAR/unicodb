<?php

namespace App\Filament\Resources\ApiLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApiLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('api_configuration_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('api_loggable_type')
                    ->searchable(),
                TextColumn::make('api_loggable_id')
                    ->searchable(),
                TextColumn::make('endpoint')
                    ->searchable(),
                TextColumn::make('method')
                    ->badge(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('status_code')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('execution_time_ms')
                    ->numeric()
                    ->sortable(),
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
