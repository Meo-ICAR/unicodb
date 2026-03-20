<?php

namespace App\Filament\Resources\ChecklistAudits\Schemas;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChecklistAuditForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SEZIONE 1: Dettagli della Checklist Audit
                Section::make('Informazioni Checklist Audit')
                    ->description('Configura i dettagli della procedura di audit/compliance')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome Procedura Audit')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nome descrittivo della procedura di audit'),
                        Textarea::make('description')
                            ->label('Descrizione')
                            ->helperText('Descrizione dettagliata degli obiettivi e scopi della checklist'),
                        Grid::make(2)->schema([
                            TextInput::make('code')
                                ->label('Codice Procedura')
                                ->maxLength(50)
                                ->helperText('Codice identificativo unico es. AUDIT_001'),
                            Select::make('type')
                                ->label('Tipo Audit')
                                ->options([
                                    'audit' => 'Verifica Ispettiva / Audit',
                                    'compliance' => 'Compliance Normativa',
                                    'internal' => 'Controllo Interno',
                                    'external' => 'Audit Esterno',
                                ])
                                ->required()
                                ->default('audit')
                                ->native(false),
                        ]),
                        Grid::make(3)->schema([
                            Toggle::make('is_template')
                                ->label('Template Riutilizzabile')
                                ->helperText('Se è un modello per creare istanze operative')
                                ->default(true)
                                ->inline(false),
                            Toggle::make('is_unique')
                                ->label('Unica per Target')
                                ->helperText('Non può essere creata più volte per lo stesso target')
                                ->default(false)
                                ->inline(false),
                            Toggle::make('is_practice')
                                ->label('Relativa a Pratica')
                                ->helperText('Specifico per pratiche/finanziamenti')
                                ->default(false)
                                ->inline(false),
                        ]),
                        // Campo nascosto per forzare is_audit = true
                        TextInput::make('is_audit_hidden')
                            ->label('Audit')
                            ->default(true)
                            ->hidden(),
                    ])
                    ->collapsible()
                    ->collapsed(),
                // SEZIONE 2: Riferimenti e Associazioni
                Section::make('Riferimenti e Associazioni')
                    ->description('Collega la checklist a documenti normativi o mandanti specifici')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('document_type_id')
                                ->label('Documento Normativo di Riferimento')
                                ->relationship('documentType', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Documento regolamentario di riferimento'),
                            Select::make('principal_id')
                                ->label('Mandante Specifico')
                                ->relationship('principal', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Se specifico per un mandante'),
                        ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
