<?php

namespace App\Filament\Resources\Audits\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;

class AuditItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'auditItems';

    protected static ?string $title = 'Dettagli Audit';

    protected static ?string $modelLabel = 'Dettaglio';

    protected static ?string $pluralModelLabel = 'Dettagli';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\TextInput::make('title')
                    ->label('Titolo')
                    ->required()
                    ->columnSpanFull(),
                \Filament\Forms\Components\Textarea::make('description')
                    ->label('Descrizione')
                    ->rows(3)
                    ->columnSpanFull(),
                \Filament\Forms\Components\Select::make('status')
                    ->label('Stato')
                    ->options([
                        'CONFORME' => 'Conforme',
                        'NON_CONFORME' => 'Non Conforme',
                        'RILIEVO' => 'Rilievo',
                        'OSSERVAZIONE' => 'Osservazione',
                    ])
                    ->default('CONFORME')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('score')
                    ->label('Punteggio')
                    ->numeric()
                    ->nullable(),
                \Filament\Forms\Components\Textarea::make('notes')
                    ->label('Note')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titolo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'CONFORME' => 'success',
                        'NON_CONFORME' => 'danger',
                        'RILIEVO' => 'warning',
                        'OSSERVAZIONE' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('score')
                    ->label('Punteggio')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Note')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
