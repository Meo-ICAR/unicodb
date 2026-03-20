<?php

namespace App\Filament\Resources\CompanyBranches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Maatwebsite\Excel\Excel;

class CompanyBranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                IconColumn::make('is_main_office')
                    ->boolean(),
                TextColumn::make('manager_first_name')
                    ->searchable(),
                TextColumn::make('manager_last_name')
                    ->searchable(),
                TextColumn::make('manager_tax_code')
                    ->searchable(),
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
                \Filament\Actions\ImportAction::make('import')
                    ->label('Importa Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->importer(\App\Filament\Imports\CompanyBranchesImporter::class)
                    ->maxRows(1000),
            ]);
    }
}
