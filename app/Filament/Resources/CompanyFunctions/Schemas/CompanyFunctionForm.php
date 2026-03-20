<?php

namespace App\Filament\Resources\CompanyFunctions\Schemas;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyFunctionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SEZIONE PRINCIPALE
                Section::make('Assegnazione Funzione')
                    ->description('Associazione tra azienda e funzione aziendale')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('function_id')
                                ->label('Funzione Aziendale')
                                ->relationship('function', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->helperText('Funzione specifica da assegnare'),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('employee_id')
                                ->label('Referente Interno')
                                ->relationship('internalEmployee', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Dipendente responsabile interno'),
                            Select::make('client_id')
                                ->label('Referente Esterno')
                                ->relationship('externalClient', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Outsourcer o consulente esterno'),
                        ]),
                    ]),
                // SEZIONE CONFIGURAZIONE
                Section::make('Configurazione Operativa')
                    ->description('Dettagli operativi e contrattuali')
                    ->schema([
                        Grid::make(2)->schema([
                            Toggle::make('is_privacy')
                                ->label('Funzione Privacy')
                                ->inline(false)
                                ->default(true)
                                ->helperText('Indica se la funzione è soggetta a normative privacy'),
                            Toggle::make('is_outsourced')
                                ->label('Esternalizzata')
                                ->inline(false)
                                ->default(false)
                                ->helperText('Indica se la funzione è gestita internamente o esternalizzata'),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('report_frequency')
                                ->label('Frequenza Report')
                                ->maxLength(50)
                                ->nullable()
                                ->helperText('Es. Mensile, Trimestrale, Semestrale'),
                            DatePicker::make('contract_expiry_date')
                                ->label('Scadenza Contratto')
                                ->nullable()
                                ->helperText('Data di scadenza del contratto di esternalizzazione'),
                        ]),
                        Textarea::make('notes')
                            ->label('Note')
                            ->maxLength(500)
                            ->nullable()
                            ->helperText("Note aggiuntive sull'assegnazione"),
                    ]),
            ]);
    }
}
