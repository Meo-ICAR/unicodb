<?php

namespace App\Filament\Resources\Companies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with('companyType'))
            ->columns([
                ImageColumn::make('logo_url')
                    ->label('Logo')
                    ->size(40)
                    ->circular()
                    ->defaultImageUrl(url('images/default-logo.png')),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('company_type.name')
                    ->label('Tipo Società')
                    ->searchable(),
                TextColumn::make('vat_number')
                    ->label('CF / Partita IVA')
                    ->searchable(),
                TextColumn::make('vat_name')
                    ->searchable(),
                TextColumn::make('oam')
                    ->searchable(),
                TextColumn::make('oam_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('oam_name')
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
