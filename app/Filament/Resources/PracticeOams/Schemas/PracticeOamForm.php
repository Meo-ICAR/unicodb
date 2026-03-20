<?php

namespace App\Filament\Resources\PracticeOams\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PracticeOamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni Generali')
                    ->description('Dettagli principali della pratica OAM')
                    ->schema([
                        TextInput::make('erogato')
                            ->label('Montante')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                        TextInput::make('erogato_lavorazione')
                            ->label('Montante Lavorazione')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                        TextInput::make('liquidato')
                            ->label('Importo')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                        TextInput::make('liquidato_lavorazione')
                            ->label('Importo Lavorazione')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                        Select::make('oam_name')
                            ->label('OAM')
                            ->options(
                                fn() => \App\Models\OamScope::query()
                                    ->orderBy('name')
                                    ->pluck('description', 'description')
                                    ->sort()
                                    ->toArray()
                            )
                            ->searchable()
                            ->nullable(),
                        TextInput::make('principal_name')
                            ->label('Nome Intermediario')
                            ->nullable(),
                        TextInput::make('CRM_code')
                            ->label('Codice CRM')
                            ->nullable(),
                        TextInput::make('practice_name')
                            ->label('Nome Pratica')
                            ->nullable(),
                        TextInput::make('type')
                            ->label('Tipo')
                            ->nullable(),
                    ])
                    ->columns(2),
                Section::make('Commissioni')
                    ->description('Importi')
                    ->schema([
                        TextInput::make('compenso')
                            ->label('Compenso')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                        TextInput::make('compenso_lavorazione')
                            ->label('Compenso Lavorazione')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                        TextInput::make('compenso_premio')
                            ->label('Compenso Premio')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                        TextInput::make('compenso_rimborso')
                            ->label('Compenso Rimborso')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                        TextInput::make('compenso_assicurazione')
                            ->label('Compenso Assicurazione')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                        TextInput::make('compenso_cliente')
                            ->label('Compenso Cliente')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                        TextInput::make('storno')
                            ->label('Storno')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                    ])
                    ->columns(4),
                Section::make('Provvigioni')
                    ->description('Importi delle provvigioni')
                    ->schema([
                        TextInput::make('provvigione')
                            ->label('Provvigione')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                        TextInput::make('provvigione_lavorazione')
                            ->label('Provvigione Lavorazione')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                        TextInput::make('provvigione_premio')
                            ->label('Provvigione Premio')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                        TextInput::make('provvigione_rimborso')
                            ->label('Provvigione Rimborso')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                        TextInput::make('provvigione_assicurazione')
                            ->label('Provvigione Assicurazione')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                        TextInput::make('provvigione_storno')
                            ->label('Provvigione Storno')
                            ->numeric()
                            ->step(0.01)
                            ->prefix('€')
                            ->nullable(),
                    ])
                    ->columns(3),
                Section::make('Date')
                    ->description('Date importanti della pratica')
                    ->schema([
                        DatePicker::make('inserted_at')
                            ->label('Data Inserimento')
                            ->nullable(),
                        DatePicker::make('accepted_at')
                            ->label('Data Accettazione')
                            ->nullable(),
                        DatePicker::make('perfected_at')
                            ->label('Data Liquidazione')
                            ->nullable(),
                        DatePicker::make('invoice_at')
                            ->label('Data Fatturazione')
                            ->nullable(),
                        DatePicker::make('canceled_at')
                            ->label('Data Annullamento')
                            ->nullable(),
                    ])
                    ->columns(4),
                Section::make('Altri Dettagli')
                    ->description('Informazioni aggiuntive')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome Mandante')
                            ->nullable(),
                        TextInput::make('tipo_prodotto')
                            ->label('Tipo Prodotto')
                            ->nullable(),
                        TextInput::make('mese')
                            ->label('Mese')
                            ->numeric()
                            ->nullable(),
                    ])
                    ->columns(2),
                Section::make('Stati')
                    ->description('Stati della pratica')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Attivo')
                            ->default(true),
                        Toggle::make('is_cancel')
                            ->label('Annullato')
                            ->default(false),
                        Toggle::make('is_perfected')
                            ->label('Perfezionato')
                            ->default(false),
                        Toggle::make('is_conventioned')
                            ->label('Convenzionato')
                            ->default(false),
                        Toggle::make('is_notconventioned')
                            ->label('Non Convenzionato')
                            ->default(false),
                        Toggle::make('is_notconvenctioned')
                            ->label('Non Convenzionato (Altro)')
                            ->default(false),
                        Toggle::make('is_invoice')
                            ->label('Fatturato')
                            ->default(false),
                        Toggle::make('is_working')
                            ->label('In Lavorazione')
                            ->default(true),
                    ])
                    ->columns(4),
            ]);
    }
}
