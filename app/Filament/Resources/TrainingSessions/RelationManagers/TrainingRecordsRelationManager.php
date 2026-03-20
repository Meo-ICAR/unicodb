<?php

namespace App\Filament\Resources\TrainingSessions\RelationManagers;

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

    protected static ?string $title = 'Partecipanti';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['employee', 'agent']))
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('employee.name')
                    ->label('Dipendente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('agent.name')
                    ->label('Agente')
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
                TextColumn::make('created_at')
                    ->label('Data Registrazione')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nuovo Partecipante'),
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
                Select::make('employee_id')
                    ->label('Dipendente')
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('agent_id')
                    ->label('Agente')
                    ->relationship('agent', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
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
