<?php

namespace App\Filament\Resources\ChecklistAudits\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
// use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
// use Filament\Forms\Components\Section;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChecklistItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'checklistItems';

    protected static ?string $title = 'Domande Checklist';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('ordine')
                    ->label('Ordine')
                    ->sortable()
                    ->width('60px'),
                TextColumn::make('item_code')
                    ->label('Codice')
                    ->searchable()
                    ->sortable()
                    ->width('100px'),
                TextColumn::make('name')
                    ->label('Domanda')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('phase')
                    ->label('Fase')
                    ->searchable()
                    ->toggleable(),
                IconColumn::make('is_required')
                    ->label('Obbl.')
                    ->boolean()
                    ->tooltip('Obbligatoria'),
                IconColumn::make('hasAttachedDocument()')
                    ->label('Doc.')
                    ->boolean()
                    ->tooltip(fn($record) => $record->hasAttachedDocument() ? 'Documento allegato: ' . $record->document->name : 'Nessun documento allegato')
                    ->trueColor('success')
                    ->falseColor('gray'),
                IconColumn::make('is_phaseclose')
                    ->label('Chius.')
                    ->boolean()
                    ->tooltip('Chiusura fase'),
                TextColumn::make('n_documents')
                    ->label('Rich.')
                    ->formatStateUsing(fn($state) => $state == 99 ? 'Multipli' : $state)
                    ->sortable(),
                TextColumn::make('attach_model')
                    ->label('Modello')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'principal' => 'Principal',
                        'agent' => 'Agente',
                        'company' => 'Azienda',
                        'audit' => 'Audit',
                        default => $state,
                    })
                    ->toggleable(),
            ])
            ->defaultSort('ordine')
            ->headerActions([
                CreateAction::make()
                    ->label('Nuova Domanda')
                    ->modalHeading('Aggiungi Domanda alla Checklist Audit')
                    ->form([
                        Section::make('Informazioni Domanda')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('item_code')
                                        ->label('Codice Unico')
                                        ->required()
                                        ->maxLength(255)
                                        ->helperText('Es. q1, doc_id'),
                                    TextInput::make('name')
                                        ->label('Titolo Domanda')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('ordine')
                                        ->label('Ordine')
                                        ->numeric()
                                        ->default(1)
                                        ->helperText('Ordine visualizzazione'),
                                ]),
                                Textarea::make('question')
                                    ->label('Testo Domanda')
                                    ->required()
                                    ->rows(3),
                                Textarea::make('description')
                                    ->label('Istruzioni Operatore')
                                    ->helperText('Istruzioni dettagliate per chi compila')
                                    ->rows(2),
                                Grid::make(3)->schema([
                                    Toggle::make('is_required')
                                        ->label('Obbligatoria')
                                        ->default(true)
                                        ->inline(false),
                                    Toggle::make('is_phaseclose')
                                        ->label('Chiusura Fase')
                                        ->default(false)
                                        ->inline(false),
                                    Select::make('n_documents')
                                        ->label('Allegati Richiesti')
                                        ->options([
                                            0 => 'Nessuno',
                                            1 => '1 Documento',
                                            99 => 'Multipli',
                                        ])
                                        ->default(0)
                                        ->native(false),
                                ]),
                                Grid::make(2)->schema([
                                    Select::make('attach_model')
                                        ->label('Modello Allegati')
                                        ->options([
                                            'principal' => 'Principal',
                                            'agent' => 'Agente',
                                            'company' => 'Azienda',
                                            'audit' => 'Audit',
                                        ])
                                        ->nullable()
                                        ->native(false),
                                    TextInput::make('phase')
                                        ->label('Fase')
                                        ->helperText('Fase della checklist'),
                                ]),
                            ])
                    ]),
            ])
            ->actions([
                Action::make('attach_document')
                    ->label('Allega Documento Esistente')
                    ->icon('heroicon-o-paper-clip')
                    ->color('success')
                    ->visible(fn($record) => $record->canAttachExistingDocument())
                    ->modalHeading('Allega Documento Esistente')
                    ->form(function ($record) {
                        $availableDocuments = $record->getAvailableDocumentsForTarget();

                        return [
                            Section::make('Seleziona Documento')
                                ->description('Scegli un documento già presente nel sistema da allegare a questa domanda')
                                ->schema([
                                    Select::make('document_id')
                                        ->label('Documento Disponibile')
                                        ->options($availableDocuments->mapWithKeys(function ($doc) {
                                            return [$doc->id => $doc->name . ' (' . $doc->documentType->name . ')'];
                                        }))
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->helperText('Documenti già caricati per ' . class_basename($record->checklist->target)),
                                    Textarea::make('attachment_note')
                                        ->label('Note Allegato')
                                        ->helperText('Note aggiuntive sul perché questo documento è stato allegato')
                                        ->rows(2),
                                ]),
                        ];
                    })
                    ->action(function ($record, array $data) {
                        $record->update([
                            'document_id' => $data['document_id'],
                            // Potresti salvare anche le note in un campo annotation o description
                            'annotation' => $data['attachment_note'] ?? null,
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Documento Allegato')
                            ->body('Il documento è stato collegato correttamente alla domanda')
                            ->send();
                    }),
                Action::make('detach_document')
                    ->label('Rimuovi Documento')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn($record) => $record->hasAttachedDocument())
                    ->requiresConfirmation()
                    ->modalHeading('Rimuovi Documento Allegato')
                    ->modalDescription('Sei sicuro di voler rimuovere il collegamento con questo documento?')
                    ->action(function ($record) {
                        $documentName = $record->document->name;
                        $record->update(['document_id' => null]);

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Documento Rimosso')
                            ->body("Il collegamento con '{$documentName}' è stato rimosso")
                            ->send();
                    }),
                EditAction::make()
                    ->modalHeading('Modifica Domanda')
                    ->form(fn($record) => [
                        // Stesso form del create ma con valori preimpostati
                        Section::make('Informazioni Domanda')
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('item_code')
                                        ->label('Codice Unico')
                                        ->required()
                                        ->maxLength(255)
                                        ->default($record->item_code),
                                    TextInput::make('name')
                                        ->label('Titolo Domanda')
                                        ->required()
                                        ->maxLength(255)
                                        ->default($record->name),
                                    TextInput::make('ordine')
                                        ->label('Ordine')
                                        ->numeric()
                                        ->default($record->ordine),
                                ]),
                                Textarea::make('question')
                                    ->label('Testo Domanda')
                                    ->required()
                                    ->rows(3)
                                    ->default($record->question),
                                Textarea::make('description')
                                    ->label('Istruzioni Operatore')
                                    ->helperText('Istruzioni dettagliate per chi compila')
                                    ->rows(2)
                                    ->default($record->description),
                                Grid::make(3)->schema([
                                    Toggle::make('is_required')
                                        ->label('Obbligatoria')
                                        ->default($record->is_required)
                                        ->inline(false),
                                    Toggle::make('is_phaseclose')
                                        ->label('Chiusura Fase')
                                        ->default($record->is_phaseclose)
                                        ->inline(false),
                                    Select::make('n_documents')
                                        ->label('Allegati Richiesti')
                                        ->options([
                                            0 => 'Nessuno',
                                            1 => '1 Documento',
                                            99 => 'Multipli',
                                        ])
                                        ->default($record->n_documents)
                                        ->native(false),
                                ]),
                                Grid::make(2)->schema([
                                    Select::make('attach_model')
                                        ->label('Modello Allegati')
                                        ->options([
                                            'principal' => 'Principal',
                                            'agent' => 'Agente',
                                            'company' => 'Azienda',
                                            'audit' => 'Audit',
                                        ])
                                        ->default($record->attach_model)
                                        ->nullable()
                                        ->native(false),
                                    TextInput::make('phase')
                                        ->label('Fase')
                                        ->helperText('Fase della checklist')
                                        ->default($record->phase),
                                ]),
                            ])
                    ]),
                DeleteAction::make(),
            ]);
    }
}
