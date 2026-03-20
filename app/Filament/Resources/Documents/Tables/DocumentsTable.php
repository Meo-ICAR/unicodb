<?php

namespace App\Filament\Resources\Documents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome Documento')
                    ->sortable()
                    ->searchable()
                    // Rende il testo blu e sottolineato come un link
                    ->color('primary')
                    ->weight('bold')
                    // Genera il link dinamico dall'URL nel database
                    ->url(fn($record): ?string => $record->url_document)
                    // Apre il documento in una nuova scheda del browser
                    ->openUrlInNewTab(),
                TextColumn::make('documentType.name')
                    ->label('Tipo Documento')
                    ->sortable()
                    ->searchable()
                    // Opzionale: anche qui puoi mettere un badge per renderlo più leggibile
                    ->badge(),
                IconColumn::make('is_signed')
                    ->label('Deve essere firmato')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Ultimo aggiornamento')
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
