<?php

namespace App\Filament\Resources\ChecklistDocuments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChecklistDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('practiceScope.name')
                    ->label('Tipo Pratica')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('documentType.name')
                    ->label('Tipo Documento')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('principal.name')
                    ->label('Banca/Ente')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Tutte'),
                IconColumn::make('is_required')
                    ->label('Obbligatorio')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Note/Condizioni')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('created_at')
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
