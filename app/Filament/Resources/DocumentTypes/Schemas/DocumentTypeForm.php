<?php

namespace App\Filament\Resources\DocumentTypes\Schemas;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocumentTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SEZIONE 1: Informazioni Base
                Section::make('Informazioni Generali')
                    ->description('Dati identificativi del tipo documento')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nome Documento')
                                ->required()
                                ->columnSpan(2),
                            TextInput::make('code')
                                ->label('Codice Mnemonico')
                                ->helperText('Es: CI = Carta Identità, VISURA = Visura Camerale')
                                ->maxLength(50),
                            TextInput::make('description')
                                ->label('Descrizione')
                                ->helperText('Descrizione aggiuntiva del documento')
                                ->maxLength(255)
                                ->columnSpan(2),
                        ]),
                    ]),
                // SEZIONE 2: Proprietà Documento
                Section::make('Proprietà Documento')
                    ->description('Caratteristiche e requisiti del documento')
                    ->schema([
                        Grid::make(3)->schema([
                            Toggle::make('is_person')
                                ->label('Persona Fisica')
                                ->helperText('Specifico per persone fisiche'),
                            Toggle::make('is_signed')
                                ->label('Richiede Firma')
                                ->helperText('Deve essere firmato'),
                            Toggle::make('is_monitored')
                                ->label('Monitoraggio Scadenza')
                                ->helperText('Scadenza da monitorare'),
                            Toggle::make('is_stored')
                                ->label('Conservazione Sostitutiva')
                                ->helperText('Richiede conservazione sostitutiva'),
                            Toggle::make('is_template')
                                ->label('Template Riutilizzabile')
                                ->helperText('Modello riutilizzabile'),
                            Toggle::make('is_sensible')
                                ->label('Dati Sensibili')
                                ->helperText('Contiene dati sensibili'),
                        ]),
                    ]),
                // SEZIONE 3: Destinatari (Target Filtering)
                Section::make('Destinatari Documento')
                    ->description('A chi si applica questo tipo di documento')
                    ->schema([
                        Grid::make(3)->schema([
                            Toggle::make('is_agent')
                                ->label('Agenti')
                                ->helperText('Applicabile agli agenti'),
                            Toggle::make('is_principal')
                                ->label('Principal')
                                ->helperText('Applicabile ai mandanti'),
                            Toggle::make('is_client')
                                ->label('Client')
                                ->helperText('Applicabile ai clienti'),
                            Toggle::make('is_practice_target')
                                ->label('Pratiche')
                                ->helperText('Applicabile alle pratiche'),
                            Toggle::make('is_company')
                                ->label('Company')
                                ->helperText('Documento aziendale generale'),
                            Toggle::make('is_practice')
                                ->label('Relativo Pratica')
                                ->helperText('Specifico per pratiche'),
                        ]),
                    ]),
                // SEZIONE 4: Emissione e Validità
                Section::make('Emissione e Validità')
                    ->description('Informazioni su ente emittente e durata')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('emitted_by')
                                ->label('Ente Rilascio')
                                ->helperText('Ente che emette il documento'),
                            TextInput::make('duration')
                                ->label('Durata Validità (giorni)')
                                ->numeric()
                                ->helperText('Validità dal rilascio in giorni'),
                        ]),
                    ]),
                // SEZIONE 5: Configurazione AI
                Section::make('Configurazione AI')
                    ->description('Impostazioni per elaborazione con intelligenza artificiale')
                    ->schema([
                        Grid::make(1)->schema([
                            Toggle::make('is_AiAbstract')
                                ->label('Genera Abstract con AI')
                                ->helperText("Chiedi all'AI di creare un abstract del documento"),
                            Toggle::make('is_AiCheck')
                                ->label('Verifica Conformità AI')
                                ->helperText('Richiede verifica di conformità tramite AI')
                                ->default(false),
                            Textarea::make('AiPattern')
                                ->label('Pattern di Riconoscimento AI')
                                ->helperText("Descrivi come l'AI può riconoscere questo tipo di documento")
                                ->rows(3)
                                ->nullable(),
                        ]),
                    ]),
            ]);
    }
}
