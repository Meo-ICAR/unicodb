<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\CompanySenders\CompanySenderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SendersRelationManager extends RelationManager
{
    protected static string $relationship = 'companySenders';

    protected static ?string $relatedResource = CompanySenderResource::class;

    protected static ?string $title = 'Mittenti';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('eventgroup')
                    ->label('Gruppo Evento')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessun gruppo'),
                IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
