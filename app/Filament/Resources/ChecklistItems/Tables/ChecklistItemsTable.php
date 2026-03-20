<?php

namespace App\Filament\Resources\ChecklistItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChecklistItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('checklist.name')
                    ->searchable(),
                TextColumn::make('ordine')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('item_code')
                    ->searchable(),
                IconColumn::make('is_required')
                    ->boolean(),
                TextColumn::make('attach_model')
                    ->badge(),
                TextColumn::make('attach_model_id')
                    ->searchable(),
                TextColumn::make('n_documents')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('repeatable_code')
                    ->searchable(),
                TextColumn::make('depends_on_code')
                    ->searchable(),
                TextColumn::make('depends_on_value')
                    ->searchable(),
                TextColumn::make('dependency_type')
                    ->badge(),
                TextColumn::make('url_step')
                    ->searchable(),
                TextColumn::make('url_callback')
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
