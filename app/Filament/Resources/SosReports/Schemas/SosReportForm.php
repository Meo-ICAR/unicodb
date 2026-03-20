<?php

namespace App\Filament\Resources\SosReports\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SosReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SEZIONE 1: Informazioni Generali
                Section::make('Informazioni Generali')
                    ->description('Dati identificativi del SOS Report')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('codice_protocollo_interno')
                                ->label('Protocollo Interno')
                                ->required()
                                ->helperText('Es: SOS-2026-001')
                                ->unique(ignoreRecord: true)
                                ->default(function () {
                                    // Genera automaticamente: SOS-ANNO-MESE-PROGRESSIVO
                                    $year = date('Y');
                                    $month = date('m');

                                    // Trova l'ultimo progressivo per questo mese
                                    $lastProgressive = \App\Models\SosReport::whereYear('created_at', '=', $year)
                                        ->whereMonth('created_at', '=', $month)
                                        ->orderBy('codice_protocollo_interno', 'desc')
                                        ->first();

                                    if ($lastProgressive) {
                                        // Estrai il numero progressivo (es: SOS-2026-001 -> 001)
                                        preg_match('/SOS-\d{4}-(\d+)/', $lastProgressive->codice_protocollo_interno, $matches);
                                        $progressive = $matches[1] ?? '001';
                                    } else {
                                        $progressive = '001';
                                    }

                                    return "SOS-{$year}-{$month}-{$progressive}";
                                }),
                            Select::make('responsabile_id')
                                ->label('Responsabile')
                                ->relationship('responsabile', 'name')
                                ->searchable()
                                ->preload()
                                ->helperText('Utente responsabile della gestione'),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('aui_record_id')
                                ->label('AUI Record')
                                ->relationship('auiRecord', 'id')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Riferimento AUI se presente'),
                            TextInput::make('client_mandate_id')
                                ->label('ID Mandato Cliente')
                                ->numeric()
                                ->nullable()
                                ->helperText('Identificativo mandato cliente'),
                        ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
                // SEZIONE 2: Classificazione Sospetto
                Section::make('Classificazione Sospetto')
                    ->description('Valutazione del grado di sospetto e stato')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('stato')
                                ->label('Stato')
                                ->options([
                                    'istruttoria' => 'Istruttoria',
                                    'archiviata' => 'Archiviata',
                                    'segnalata_uif' => 'Segnalata UIF',
                                ])
                                ->default('istruttoria')
                                ->required()
                                ->helperText('Stato attuale del report'),
                            Select::make('grado_sospetto')
                                ->label('Grado Sospetto')
                                ->options([
                                    'basso' => 'Basso',
                                    'medio' => 'Medio',
                                    'alto' => 'Alto',
                                ])
                                ->default('basso')
                                ->required()
                                ->helperText('Livello di sospetto rilevato'),
                        ]),
                        Textarea::make('motivo_sospetto')
                            ->label('Motivo del Sospetto')
                            ->required()
                            ->rows(4)
                            ->helperText("Descrizione dettagliata dell'anomalia riscontrata"),
                    ])
                    ->collapsible()
                    ->collapsed(),
                // SEZIONE 3: Gestione UIF
                Section::make('Gestione UIF')
                    ->description('Informazioni relative alla segnalazione UIF')
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('data_segnalazione_uif')
                                ->label('Data Segnalazione UIF')
                                ->helperText('Data di invio alla UIF')
                                ->placeholder('Non ancora segnalata'),
                            TextInput::make('protocollo_uif')
                                ->label('Protocollo UIF')
                                ->helperText('Riferimento portale INFOSTAT/UIF')
                                ->placeholder('Nessun protocollo'),
                        ]),
                        Textarea::make('decisione_finali')
                            ->label('Decisioni Finali')
                            ->rows(4)
                            ->helperText('Note e decisioni finali del Responsabile AML')
                            ->placeholder('Nessuna decisione finale'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
