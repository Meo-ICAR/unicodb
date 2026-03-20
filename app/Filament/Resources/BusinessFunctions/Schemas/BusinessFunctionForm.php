<?php

namespace App\Filament\Resources\BusinessFunctions\Schemas;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BusinessFunctionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SEZIONE PRINCIPALE
                Section::make('Informazioni Generali')
                    ->description('Dati principali della funzione aziendale')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('code')
                                ->label('Codice Univoco')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->helperText('Codice identificativo univoco es. GOV-CDA')
                                ->maxLength(10),
                            Select::make('macro_area')
                                ->label('Macro Area')
                                ->options([
                                    'Governance' => 'Governance',
                                    'Business / Commerciale' => 'Business / Commerciale',
                                    'Supporto' => 'Supporto',
                                    'Controlli (II Livello)' => 'Controlli (II Livello)',
                                    'Controlli (III Livello)' => 'Controlli (III Livello)',
                                    'Controlli / Privacy' => 'Controlli / Privacy',
                                ])
                                ->required()
                                ->searchable()
                                ->helperText('Area organizzativa di appartenenza'),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('name')
                                ->label('Nome Funzione')
                                ->options([
                                    'Consiglio di Amministrazione / Direzione' => 'Consiglio di Amministrazione / Direzione',
                                    'Direzione Commerciale' => 'Direzione Commerciale',
                                    'Gestione Rete e Collaboratori' => 'Gestione Rete e Collaboratori',
                                    'Back Office / Istruttoria Pratiche' => 'Back Office / Istruttoria Pratiche',
                                    'Amministrazione e Contabilità' => 'Amministrazione e Contabilità',
                                    'IT e Sicurezza Dati' => 'IT e Sicurezza Dati',
                                    'Marketing e Comunicazione' => 'Marketing e Comunicazione',
                                    'Gestione Reclami e Controversie' => 'Gestione Reclami e Controversie',
                                    'Risorse Umane (HR) e Formazione' => 'Risorse Umane (HR) e Formazione',
                                    'Compliance (Conformità)' => 'Compliance (Conformità)',
                                    'Risk Management' => 'Risk Management',
                                    'Antiriciclaggio (AML)' => 'Antiriciclaggio (AML)',
                                    'Internal Audit (Revisione Interna)' => 'Internal Audit (Revisione Interna)',
                                    'Data Protection Officer (DPO)' => 'Data Protection Officer (DPO)',
                                ])
                                ->required()
                                ->searchable()
                                ->helperText('Nome specifico della funzione'),
                            Select::make('type')
                                ->label('Tipo Funzione')
                                ->options([
                                    'Strategica' => 'Strategica',
                                    'Operativa' => 'Operativa',
                                    'Supporto' => 'Supporto',
                                    'Controllo' => 'Controllo',
                                ])
                                ->required()
                                ->searchable()
                                ->helperText('Tipologia di funzione aziendale'),
                        ]),
                    ]),
                // SEZIONE DESCRIZIONE
                Section::make('Descrizione e Configurazione')
                    ->description('Dettagli operativi e stato esternalizzazione')
                    ->schema([
                        Textarea::make('description')
                            ->label('Descrizione Dettagliata')
                            ->rows(3)
                            ->helperText('Descrizione completa delle attività e responsabilità'),
                        Grid::make(2)->schema([
                            Select::make('outsourcable_status')
                                ->label('Stato Esternalizzazione')
                                ->options([
                                    'no' => 'Non Esternalizzabile',
                                    'si' => 'Esternalizzabile',
                                    'parziale' => 'Parzialmente Esternalizzabile',
                                ])
                                ->default('no')
                                ->helperText('Possibilità di esternalizzare la funzione'),
                        ]),
                    ]),
                // SEZIONE GESTIONE E RESPONSABILITÀ
                Section::make('Gestione e Responsabilità')
                    ->description('Informazioni sulla gestione e responsabilità della funzione')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('managed_by_code')
                                ->label('Gestita da Funzione')
                                ->options(function () {
                                    return \App\Models\BusinessFunction::pluck('name', 'code')->toArray();
                                })
                                ->searchable()
                                ->placeholder('Nessuna funzione gestore')
                                ->helperText('Codice della funzione che gestisce questa funzione'),
                        ]),
                        Grid::make(1)->schema([
                            Textarea::make('mission')
                                ->label('Missione della Funzione')
                                ->rows(3)
                                ->helperText('Cosa fa la funzione e quale è il suo scopo principale'),
                        ]),
                        Grid::make(1)->schema([
                            Textarea::make('responsibility')
                                ->label('Responsabilità e Attività')
                                ->rows(4)
                                ->helperText('Elenco delle attività e responsabilità specifiche della funzione'),
                        ]),
                    ]),
            ]);
    }
}
