<?php

namespace App\Filament\Resources\SoftwareApplications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SoftwareApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('softwareCategory.name')
                    ->label('Categoria')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('name')
                    ->label('Software')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('provider_name')
                    ->label('Provider')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('website_url')
                    ->label('Sito Web')
                    //  ->url()
                    ->limit(30)
                    ->toggleable()
                    ->openUrlInNewTab(),
                IconColumn::make('is_cloud')
                    ->label('Cloud')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('api_url')
                    ->label('URL API')
                    //  ->url()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sandbox_url')
                    ->label('Sandbox')
                    //    ->url()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('api_key_url')
                    ->label('API Key')
                    //   ->url()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Aggiornato il')
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
