<?php

namespace App\Filament\Resources\ComplianceViolations\Schemas;

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

class ComplianceViolationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni Generali')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('user_id')
                                ->label('Utente')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Utente che ha causato la violazione (se applicabile)'),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('violation_type')
                                ->label('Tipo Violazione')
                                ->options([
                                    'accesso_non_autorizzato' => 'Accesso Non Autorizzato',
                                    'kyc_scaduto' => 'KYC Scaduto',
                                    'forzatura_stato' => 'Forzatura Stato',
                                    'data_breach' => 'Data Breach',
                                    'violazione_privacy' => 'Violazione Privacy',
                                    'accesso_abusivo' => 'Accesso Abusivo',
                                ])
                                ->required()
                                ->reactive(),
                            Select::make('severity')
                                ->label('Gravità')
                                ->options([
                                    'basso' => 'Basso',
                                    'medio' => 'Medio',
                                    'alto' => 'Alto',
                                    'critico' => 'Critico',
                                ])
                                ->default('medio')
                                ->required(),
                        ]),
                        Textarea::make('description')
                            ->label('Descrizione')
                            ->rows(4)
                            ->required()
                            ->helperText("Descrizione dettagliata dell'evento"),
                    ]),
                Section::make('Entità Coinvolta (Polimorfica)')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('violatable_type')
                                ->label('Tipo Entità')
                                ->options([
                                    'App\Models\Client' => 'Cliente',
                                    'App\Models\Dossier' => 'Dossier',
                                    'App\Models\Practice' => 'Pratica',
                                    'App\Models\Document' => 'Documento',
                                    'App\Models\Checklist' => 'Checklist',
                                ])
                                ->reactive()
                                ->afterStateUpdated(fn($state, callable $set) => $set('violatable_id', null))
                                ->helperText('Tipo di entità violata'),
                            Select::make('violatable_id')
                                ->label('Entità Selezionata')
                                ->searchable()
                                ->getSearchResultsUsing(function (string $search, callable $get) {
                                    $type = $get('violatable_type');
                                    if (!$type)
                                        return [];

                                    $model = new $type;
                                    return $model::where('name', 'like', "%{$search}%")
                                        ->limit(50)
                                        ->pluck('name', 'id');
                                })
                                ->getOptionLabelUsing(function ($value, callable $get) {
                                    $type = $get('violatable_type');
                                    if (!$type || !$value)
                                        return '';

                                    $model = new $type;
                                    $record = $model::find($value);
                                    return $record?->name ?? '';
                                })
                                ->helperText('Entità specifica violata'),
                        ]),
                    ]),
                Section::make('Dati Tecnici e Legali')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('ip_address')
                                ->label('Indirizzo IP')
                                ->helperText('Indirizzo IP sorgente'),
                            TextInput::make('user_agent')
                                ->label('User Agent (Browser)')
                                ->helperText('Browser e dispositivo utilizzato'),
                        ]),
                        DateTimePicker::make('discovery_date')
                            ->label('Data Scoperta')
                            ->helperText('Data e ora di scoperta della violazione'),
                    ]),
                Section::make('Gestione Risoluzione')
                    ->schema([
                        Grid::make(2)->schema([
                            DateTimePicker::make('resolved_at')
                                ->label('Data Risoluzione')
                                ->helperText('Quando la violazione è stata risolta'),
                            Select::make('resolved_by')
                                ->label('Risolto Da')
                                ->relationship('resolvedBy', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Utente che ha risolto la violazione'),
                        ]),
                        Textarea::make('resolution_notes')
                            ->label('Note Risoluzione')
                            ->rows(3)
                            ->helperText('Dettagli sulla risoluzione della violazione'),
                    ]),
            ]);
    }
}
