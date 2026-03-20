<?php

namespace App\Filament\Resources\SoftwareCategories\RelationManagers;

use App\Models\SoftwareApplication;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class SoftwareApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'softwareApplications';

    protected static ?string $title = 'Software Associati';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Software')
                    ->searchable(),
                Tables\Columns\TextColumn::make('version')
                    ->label('Versione')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrizione')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\AssociateAction::make()
                    ->label('Associa Software Esistente'),
                Tables\Actions\CreateAction::make()
                    ->label('Crea Nuovo Software'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                \Filament\Actions\DissociateAction::make()
                    ->label('Dissocia'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    \Filament\Actions\DissociateBulkAction::make(),
                ]),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nome Software'),
                \Filament\Forms\Components\TextInput::make('provider_name')
                    ->label('Produttore'),
                \Filament\Forms\Components\TextInput::make('website_url')
                    ->url()
                    ->label('Sito Web'),
            ]);
    }
}
