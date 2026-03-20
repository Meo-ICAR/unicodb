<?php

namespace App\Filament\Resources\PracticeCommissionStatuses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PracticeCommissionStatusesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('status_payment')
                    ->label('Stato Pagamento')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Codice')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_perfectioned')
                    ->label('Perfezionata')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->color(fn($state) => $state ? 'success' : 'gray'),
                IconColumn::make('is_working')
                    ->label('In Lavorazione')
                    ->boolean()
                    ->trueIcon('heroicon-s-clock')
                    ->falseIcon('heroicon-o-pause-circle')
                    ->color(fn($state) => $state ? 'warning' : 'gray'),
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
