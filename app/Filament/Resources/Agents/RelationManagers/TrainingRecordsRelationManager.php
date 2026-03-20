<?php

namespace App\Filament\Resources\Agents\RelationManagers;

use App\Models\Agent;
use App\Models\TrainingRecord;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainingRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'trainingRecords';

    protected static ?string $title = 'Formazione Frequentata';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('trainable_type', Agent::class)->with('trainingSession'))
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('trainingSession.name')
                    ->label('Sessione Formativa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'COMPLETATO' => 'success',
                        'FREQUENTANTE' => 'warning',
                        'ISCRITTO' => 'info',
                        'NON_SUPERATO' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hours_attended')
                    ->label('Ore Frequentate')
                    ->sortable(),
                TextColumn::make('completion_date')
                    ->label('Data Completamento')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nuova Registrazione'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Modifica'),
                DeleteAction::make()
                    ->label('Elimina'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Elimina Selezionati'),
                ]),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('training_session_id')
                    ->label('Sessione Formativa')
                    ->relationship('trainingSession', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->label('Stato')
                    ->options([
                        'ISCRITTO' => 'Iscritto',
                        'FREQUENTANTE' => 'Frequentante',
                        'COMPLETATO' => 'Completato',
                        'NON_SUPERATO' => 'Non Superato',
                    ])
                    ->default('ISCRITTO')
                    ->required(),
                TextInput::make('hours_attended')
                    ->label('Ore Frequentate')
                    ->numeric()
                    ->default(0),
                TextInput::make('score')
                    ->label('Punteggio/ Esito')
                    ->maxLength(50),
                TextInput::make('completion_date')
                    ->label('Data Completamento')
                    ->type('date'),
                TextInput::make('certificate_path')
                    ->label('Percorso Certificato')
                    ->maxLength(255),
            ]);
    }
}
