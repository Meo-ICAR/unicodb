<?php

namespace App\Filament\Resources\TrainingRecords\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class TrainingRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sessione Formativa')
                    ->schema([
                        Select::make('training_session_id')
                            ->label('Sessione Formativa')
                            ->relationship('trainingSession', 'title')
                            ->searchable()
                            ->required(),
                    ]),
                Section::make('Partecipante (Polimorfico)')
                    ->schema([
                        Select::make('trainable_type')
                            ->label('Tipo Partecipante')
                            ->options([
                                'App\Models\Employee' => 'Dipendente',
                                'App\Models\Agent' => 'Agente',
                                'App\Models\Company' => 'Azienda',
                                'App\Models\Client' => 'Cliente',
                            ])
                            ->reactive()
                            ->afterStateUpdated(fn($state, callable $set) => $set('trainable_id', null))
                            ->required(),
                        Select::make('trainable_id')
                            ->label('Partecipante Selezionato')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search, callable $get) {
                                $type = $get('trainable_type');
                                if (!$type)
                                    return [];

                                $model = new $type;
                                return $model::where('name', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->pluck('name', 'id');
                            })
                            ->getOptionLabelUsing(function ($value, callable $get) {
                                $type = $get('trainable_type');
                                if (!$type || !$value)
                                    return '';

                                $model = new $type;
                                $record = $model::find($value);
                                return $record?->name ?? '';
                            })
                            ->required(),
                    ]),
                Section::make('Riferimenti Specifici (Legacy)')
                    ->schema([
                        Select::make('employee_id')
                            ->label('Dipendente (Legacy)')
                            ->relationship('employee', 'name')
                            ->searchable()
                            ->nullable(),
                        Select::make('agent_id')
                            ->label('Agente (Legacy)')
                            ->relationship('agent', 'name')
                            ->searchable()
                            ->nullable(),
                    ])
                    ->collapsed(),
                Section::make('Dettagli Formazione')
                    ->schema([
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
                        TextInput::make('name')
                            ->label('Descrizione')
                            ->nullable(),
                        TextInput::make('hours_attended')
                            ->label('Ore Frequentate')
                            ->numeric()
                            ->default(0)
                            ->step(0.5),
                        TextInput::make('score')
                            ->label('Punteggio/Esito')
                            ->nullable(),
                        DatePicker::make('completion_date')
                            ->label('Data Completamento')
                            ->nullable(),
                        TextInput::make('certificate_path')
                            ->label('Percorso Certificato')
                            ->nullable(),
                    ]),
            ]);
    }
}
