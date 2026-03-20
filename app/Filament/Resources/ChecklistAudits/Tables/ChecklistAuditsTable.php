<?php

namespace App\Filament\Resources\ChecklistAudits\Tables;

use App\Models\Checklist;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class ChecklistAuditsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(fn() => Checklist::query()
                ->where('type', 'audit')
                ->where('is_audit', '=', true)
                ->whereNotNull('target_id'))
            ->columns([
                TextColumn::make('target.name')
                    ->label('Soggetto')
                    ->searchable()
                    ->placeholder('Nessun target'),
                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'da_compilare' => 'danger',
                        'in_corso' => 'warning',
                        'completata' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('code')
                    ->label('Codice')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Creata il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Aggiornata il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label('Procedura Audit')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                //  BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                //  ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
