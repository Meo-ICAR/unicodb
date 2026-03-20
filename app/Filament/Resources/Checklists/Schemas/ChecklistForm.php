<?php

namespace App\Filament\Resources\Checklists\Schemas;

use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ChecklistForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // SEZIONE 1: Dettagli del Template
                Section::make(function ($get) {
                    return 'CHECKLIST : ' . $get('name');
                })
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome della Checklist')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Descrizione generale'),
                        Select::make('type')
                            ->label('Tipo di utilizzo')
                            ->options([
                                'loan_management' => 'Gestione Pratica / Finanziamento',
                                'audit' => 'Verifica Ispettiva / Audit',
                            ])
                            ->required()
                            ->native(false),
                        Grid::make(2)->schema([
                            Toggle::make('is_practice')
                                ->label('Riferita a una Pratica')
                                ->inline(false)
                                ->default(false),
                            Toggle::make('is_audit')
                                ->label('Riferita a un Audit / Compliance')
                                ->inline(false)
                                ->default(false),
                        ]),
                        Toggle::make('is_unique')
                            ->label('Checklist Unica per Target')
                            ->helperText('Se attivato, questa checklist non può essere creata più volte per lo stesso target (agente, pratica, ecc.)')
                            ->default(false)
                            ->inline(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state && $get('name')) {
                                    $set('unique_warning', '⚠️ Attenzione: questa checklist potrà essere assegnata solo una volta per ogni target (agente, pratica, ecc.)');
                                } else {
                                    $set('unique_warning', null);
                                }
                            }),
                        Placeholder::make('unique_warning')
                            ->content(fn($get) => $get('unique_warning'))
                            ->visible(fn($get) => filled($get('unique_warning')))
                            ->columnSpanFull(),
                        Grid::make(2)->schema([
                            Select::make('document_type_id')
                                ->label('Riferimento della checklist nel  Documento regolamentorio (Opzionale)')
                                ->relationship('documentType', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Template documento es. manuale operativo'),
                            Select::make('document_id')
                                ->label('Riferimento specifico al regolamento (Opzionale)')
                                ->relationship('document', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Documento operativo'),
                            TextInput::make('code')
                                ->label('Codice Checklist (Opzionale)')
                                ->maxLength(255)
                                ->helperText('Codice identificativo interno'),
                            Select::make('principal_id')
                                ->label('Mandante Specifico (Opzionale)')
                                ->relationship('principal', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Se la checklist è specifica per un mandante es. loro audit'),
                        ]),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
                //   ->columns(2),
                // SEZIONE 2: Domande / Items (Repeater)
                Section::make('Domande ed Elementi della Checklist')
                    ->description("Trascina gli elementi per riordinarli. L'ordine verrà salvato automaticamente.")
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('items')
                            ->relationship('checklistItems')  // Punta alla relazione HasMany nel modello Checklist
                            ->schema([
                                // Riga 1: Dati Base Domanda
                                Grid::make(3)->schema([
                                    TextInput::make('item_code')
                                        ->label('Codice Univoco (es. doc_id, q1)')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('name')
                                        ->label('Titolo / Nome Breve')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('ordine')
                                        ->label('Ordine')
                                        ->nullable()
                                        ->numeric()
                                        ->helperText('Ordine di visualizzazione'),
                                ]),
                                Grid::make(2)->schema([
                                    TextInput::make('phase')
                                        ->label('Fase della checklist')
                                        ->nullable()
                                        ->helperText('Fase a cui appartiene questo elemento'),
                                    Toggle::make('is_phaseclose')
                                        ->label('Attività di chiusura fase')
                                        ->default(false)
                                        ->inline(false)
                                        ->helperText("Se è l'attività finale della fase"),
                                ]),
                                Textarea::make('question')
                                    ->label('Testo della Domanda / Richiesta')
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Textarea::make('description')
                                    ->label("Istruzioni per l'operatore (Opzionale)")
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Textarea::make('descriptioncheck')
                                    ->label('Descrizione Verifica Conformità')
                                    ->helperText('Descrizione verifica conformità da effettuare')
                                    ->rows(2)
                                    ->columnSpanFull(),
                                // Riga 2: Impostazioni Allegati e Obbligatorietà
                                Grid::make(3)->schema([
                                    Toggle::make('is_required')
                                        ->label('Risposta Obbligatoria')
                                        ->inline(false)
                                        ->default(true),
                                    Select::make('n_documents')
                                        ->label('Allegati Richiesti')
                                        ->options([
                                            0 => 'Nessun allegato (Solo risposta testo/vero-falso)',
                                            1 => 'Esattamente 1 Documento',
                                            99 => 'Documenti Multipli Consentiti',
                                        ])
                                        ->required()
                                        ->default(0)
                                        ->native(false)
                                        ->live(),
                                    Select::make('attach_model')
                                        ->label('Modello di destinazione file')
                                        ->options([
                                            'principal' => 'Cliente (Principal)',
                                            'agent' => 'Agente / Collaboratore',
                                            'company' => 'Azienda',
                                            'audit' => 'Verifica Ispettiva (Audit)',
                                        ])
                                        ->native(false)
                                        // Mostra il campo solo se n_documents > 0
                                        ->visible(fn(Get $get) => $get('n_documents') > 0)
                                        ->required(fn(Get $get) => $get('n_documents') > 0),
                                ]),
                                // Riga 3: Logica Condizionale
                                Section::make('Logica Condizionale')
                                    ->schema([
                                        Select::make('dependency_type')
                                            ->label('Comportamento')
                                            ->options([
                                                'show_if' => 'Mostra questa domanda solo se...',
                                                'hide_if' => 'Nascondi questa domanda se...',
                                            ])
                                            ->native(false)
                                            ->live(),
                                        TextInput::make('depends_on_code')
                                            ->label('Codice della domanda precedente')
                                            ->helperText('Inserisci il codice univoco della domanda da cui dipende')
                                            ->visible(fn(Get $get) => filled($get('dependency_type')))
                                            ->required(fn(Get $get) => filled($get('dependency_type'))),
                                        TextInput::make('depends_on_value')
                                            ->label('Valore atteso')
                                            ->helperText('Es. 1 per Vero, 0 per Falso')
                                            ->visible(fn(Get $get) => filled($get('dependency_type')))
                                            ->required(fn(Get $get) => filled($get('dependency_type'))),
                                    ])
                                    ->columns(3)
                                    ->collapsible()
                                    ->collapsed(),  // Chiuso di default per mantenere pulita l'interfaccia
                            ])
                            ->orderColumn('ordine')  // Salva automaticamente l'ordine usando la colonna `ordine` (drag and drop)
                            ->defaultItems(1)
                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? 'Nuova Voce')
                            ->collapsible()
                            ->cloneable()  // Utile per duplicare domande simili (es. i documenti)
                            ->columnSpanFull()
                    ]),
            ]);
    }
}
