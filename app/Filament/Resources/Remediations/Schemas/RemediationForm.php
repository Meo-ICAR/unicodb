<?php

namespace App\Filament\Resources\Remediations\Schemas;

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

class RemediationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SEZIONE PRINCIPALE
                Section::make('Informazioni Generali')
                    ->description("Dati principali dell'azione di rimedio")
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('audit_item_id')
                                ->label('Audit item di Riferimento')
                                ->relationship('auditItem', 'title')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Audit item a cui è associata questa azione di rimedio'),
                            Select::make('business_function_id')
                                ->label('Funzione Aziendale')
                                ->relationship('businessFunction', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText("Funzione aziendale responsabile dell'azione"),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('remediation_type')
                                ->label('Tipo Rimedio')
                                ->options([
                                    'AML' => 'Antiriciclaggio',
                                    'Gestione Reclami' => 'Gestione Reclami',
                                    'Monitoraggio Rete' => 'Monitoraggio Rete',
                                    'Privacy' => 'Privacy',
                                    'Trasparenza' => 'Trasparenza',
                                    'Assetto Organizzativo' => 'Assetto Organizzativo',
                                ])
                                ->required()
                                ->searchable()
                                ->helperText("Categoria dell'azione di rimedio"),
                            TextInput::make('name')
                                ->label('Nome Azione')
                                ->required()
                                ->maxLength(255)
                                ->helperText("Nome descrittivo dell'azione di rimedio"),
                        ]),
                    ]),
                // SEZIONE DESCRIZIONE
                Section::make('Dettagli Azione')
                    ->description('Descrizione completa e tempi di esecuzione')
                    ->schema([
                        Textarea::make('description')
                            ->label('Descrizione Dettagliata')
                            ->required()
                            ->rows(4)
                            ->helperText("Descrizione completa dell'azione da intraprendere"),
                        Grid::make(2)->schema([
                            TextInput::make('timeframe_hours')
                                ->label('Tempo (Ore)')
                                ->numeric()
                                ->nullable()
                                ->helperText("Tempo stimato in ore per completare l'azione"),
                            TextInput::make('timeframe_desc')
                                ->label('Descrizione Tempo')
                                ->maxLength(100)
                                ->nullable()
                                ->helperText('Descrizione testuale del tempo richiesto'),
                        ]),
                    ]),
            ]);
    }
}
