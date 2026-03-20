<?php

namespace App\Filament\Resources\ClientTypes\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ClientTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Nome Tipo Cliente'),
                Section::make('Tipologia')
                    ->description('Definisci la natura del tipo di cliente')
                    ->schema([
                        Toggle::make('is_person')
                            ->label('Persona Fisica')
                            ->helperText('Indica se questo tipo di cliente è una persona fisica')
                            ->default(true),
                        Toggle::make('is_company')
                            ->label('Società/Azienda')
                            ->helperText('Indica se questo tipo di cliente è una società o azienda')
                            ->default(false),
                    ]),
                Section::make('Configurazione Privacy')
                    ->description('Imposta i parametri privacy GDPR per questo tipo di cliente')
                    ->schema([
                        Textarea::make('privacy_data')
                            ->label('Dati Privacy Specifici')
                            ->helperText('Informazioni privacy specifiche per questo tipo di cliente')
                            ->rows(3),
                        TextInput::make('privacy_role')
                            ->label('Ruolo Privacy')
                            ->helperText('es. Titolare Autonomo, Responsabile Esterno'),
                        Textarea::make('purpose')
                            ->label('Finalità del Trattamento')
                            ->helperText('Descrivi le finalità del trattamento dati')
                            ->rows(3),
                        Textarea::make('data_subjects')
                            ->label('Categorie di Interessati')
                            ->helperText('Specifica le categorie di interessati')
                            ->rows(2),
                        Textarea::make('data_categories')
                            ->label('Categorie di Dati Trattati')
                            ->helperText('Specifica le categorie di dati trattati')
                            ->rows(2),
                        TextInput::make('retention_period')
                            ->label('Tempi di Conservazione')
                            ->helperText('es. 10 anni, 5 anni dalla cessazione rapporto'),
                        TextInput::make('extra_eu_transfer')
                            ->label('Trasferimento Extra-UE')
                            ->helperText("Specificare se avvengono trasferimenti fuori dall'UE"),
                        Textarea::make('security_measures')
                            ->label('Misure di Sicurezza')
                            ->helperText('Descrivi le misure di sicurezza implementate')
                            ->rows(3),
                    ])
                    ->collapsible(),
            ]);
    }
}
