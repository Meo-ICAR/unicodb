<?php

namespace App\Filament\Resources\RuiWebSites\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RuiWebSitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // numero_iscrizione_rui	varchar(255)	Numero iscrizione RUI
                TextColumn::make('numero_iscrizione_rui')
                    ->searchable(),
                // web_url	varchar(255)	Web URL
                TextColumn::make('web_url')
                    ->searchable(),
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
