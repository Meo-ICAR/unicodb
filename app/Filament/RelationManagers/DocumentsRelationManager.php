<?php

namespace App\Filament\RelationManagers;

use App\Filament\Actions\BulkClassifyDocumentsAction;
use App\Filament\Actions\ClassifyDocumentAction;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Principal;
use App\Models\Website;
use App\Services\DocumentClassificationService;
use App\Traits\HasDocumentTypeFiltering;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Filter;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    use HasDocumentTypeFiltering;

    protected static string $relationship = 'documents';

    protected static ?string $title = 'Documenti';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nome Documento')
                    ->sortable()
                    ->searchable()
                    ->color('primary')
                    ->weight('bold')
                    ->url(fn($record): ?string => $record->url_document)
                    ->openUrlInNewTab(),
                TextColumn::make('documentType.name')
                    ->label('Tipo Documento')
                    ->sortable()
                    ->searchable()
                    ->badge(),
                TextColumn::make('documentStatus.name')
                    ->label('Stato')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn($record) => $record->documentStatus?->getStatusClass() ?? 'gray'),
                IconColumn::make('is_signed')
                    ->label('Firmato')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('expires_at')
                    ->label('Scadenza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('verified_at')
                    ->label('Data Verifica')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Non verificato'),
            ])
            ->filters([])  // headerFilters
            ->headerActions([
                CreateAction::make()
                    ->steps([
                        Step::make('Name')
                            ->description('Carica documento')
                            ->schema([
                                Select::make('document_type_id')
                                    ->label('Tipo Documento')
                                    ->options(function () {
                                        $ownerRecord = $this->getOwnerRecord();
                                        return $this->getFilteredDocumentTypes($ownerRecord);
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $documentType = DocumentType::find($state);
                                        if ($documentType && empty($get('name'))) {
                                            $set('name', $documentType->name);
                                        }
                                    })
                                    ->columnSpan(2),
                                Hidden::make('documentable_type')
                                    ->default(fn() => get_class($this->getOwnerRecord())),
                                Hidden::make('documentable_id')
                                    ->default(fn() => $this->getOwnerRecord()->id),
                                Hidden::make('uploaded_by')
                                    ->default(fn() => auth()->id()),
                            ]),
                        Step::make('Description')
                            ->description('Compila informazioni documento')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('name')
                                        ->label('Nome Documento')
                                        ->columnSpan(2),
                                    Select::make('document_status_id')
                                        ->label('Stato Documento')
                                        ->relationship('documentStatus', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->nullable(),
                                    Toggle::make('is_signed')
                                        ->label('Firmato')
                                        ->default(false),
                                    DatePicker::make('emitted_at')
                                        ->label('Data Emissione')
                                        ->default(now()),
                                    DatePicker::make('expires_at')
                                        ->label('Data Scadenza'),
                                    TextInput::make('emitted_by')
                                        ->label('Ente Rilascio'),
                                    TextInput::make('docnumber')
                                        ->label('Numero Documento'),
                                ]),
                                Textarea::make('description')
                                    ->label('Descrizione')
                                    ->rows(3)
                                    ->nullable(),
                                Section::make('Carica File')
                                    ->description('Carica il documento in formato PDF, immagine o Word')
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('document')
                                            ->label('File Documento')
                                            ->collection('documents')
                                            ->disk('public')
                                            ->preserveFilenames()
                                            ->downloadable()
                                            ->previewable(true)
                                            ->imageEditor()
                                            ->maxSize(10240)  // 10MB
                                            ->acceptedFileTypes([
                                                'application/pdf',
                                                'image/jpeg',
                                                'image/png',
                                                'image/jpg',
                                                'application/msword',
                                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                            ])
                                            ->required(),
                                    ])
                            ])
                    ]),
                Action::make('classify_company_documents')
                    ->label('Classifica')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Classifica Documenti')
                    ->modalDescription('Classifica tutti i documenti non classificati  usando le regole dei tipi documento')
                    ->modalSubmitActionLabel('Avvia Classificazione')
                    ->action(function () {
                        $ownerRecord = $this->getOwnerRecord();
                        $classificationService = new DocumentClassificationService();

                        try {
                            // Get company_id from the owner record
                            $companyId = null;
                            if (method_exists($ownerRecord, 'company_id')) {
                                $companyId = $ownerRecord->company_id;
                            } elseif (method_exists($ownerRecord, 'company')) {
                                $companyId = $ownerRecord->company?->id;
                            } elseif ($ownerRecord instanceof \App\Models\Company) {
                                $companyId = $ownerRecord->id;
                            }

                            if (!$companyId) {
                                Notification::make()
                                    ->title('Errore Company ID')
                                    ->body('Impossibile determinare la company ID dal record corrente')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Get unclassified documents for this company
                            $documents = Document::whereNull('document_type_id')
                                ->whereHasMorph('documentable', ['App\Models\Company', 'App\Models\Agent', 'App\Models\Principal',
                                        'App\Models\Client', 'App\Models\Practice'], function ($query) use ($companyId) {
                                    if ($query->getModel() instanceof \App\Models\Company) {
                                        $query->where('id', $companyId);
                                    } else {
                                        $query->where('company_id', $companyId);
                                    }
                                })
                                ->get();

                            $classified = 0;
                            $unclassified = 0;

                            foreach ($documents as $document) {
                                $success = $classificationService->classifySingleDocument($document);

                                if ($success) {
                                    $classified++;
                                } else {
                                    $unclassified++;
                                }
                            }

                            $message = "Classificazione company completata!\n"
                                . "Documenti processati: {$documents->count()}\n"
                                . "Classificati: {$classified}\n"
                                . "Non classificati: {$unclassified}";

                            Notification::make()
                                ->title('Classificazione Company Completata')
                                ->body($message)
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Errore Classificazione Company')
                                ->body('Si è verificato un errore: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])  // Action::make('create_document')
            ->actions([
                // AZIONE PER VEDERE IL DOCUMENTO
                EditAction::make()
                    ->label('Modifica')
                    ->modalHeading('Modifica Documento'),
                ClassifyDocumentAction::make()
                    ->visible(fn($record) => $record && $record->document_type_id === null),
                DeleteAction::make()
                    ->label('Elimina')
                    ->requiresConfirmation()
                    ->modalHeading('Elimina Documento')
                    ->modalDescription('Sei sicuro di voler eliminare questo documento? Questa azione non è reversibile.')
                    ->modalSubmitActionLabel('Sì, elimina')
                    ->modalCancelActionLabel('Annulla'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkClassifyDocumentsAction::make(),
                    DeleteBulkAction::make()
                        ->label('Elimina Selezionati'),
                ])
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni Documento')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('document_type_id')
                                ->label('Tipo Documento')
                                ->relationship('documentType', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                            Select::make('document_status_id')
                                ->label('Stato Documento')
                                ->relationship('documentStatus', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                            Toggle::make('is_signed')
                                ->label('Firmato')
                                ->default(false),
                            TextInput::make('name')
                                ->label('Nome Documento')
                                ->columnSpan(2),
                            TextInput::make('docnumber')
                                ->label('Numero Documento'),
                            TextInput::make('emitted_by')
                                ->label('Ente Rilascio'),
                            DatePicker::make('emitted_at')
                                ->label('Data Emissione'),
                            DatePicker::make('expires_at')
                                ->label('Data Scadenza'),
                        ]),
                        Textarea::make('description')
                            ->label('Descrizione')
                            ->rows(3)
                            ->nullable(),
                        Textarea::make('annotation')
                            ->label('Annotazioni Interne')
                            ->rows(2)
                            ->nullable(),
                        Textarea::make('rejection_note')
                            ->label('Note Rifiuto')
                            ->rows(2)
                            ->nullable()
                            ->helperText('Motivazioni del rifiuto del documento'),
                    ]),
                Section::make('File Documento')
                    ->description('Carica il documento in formato PDF, immagine o Word')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('document')
                            ->label('File Documento')
                            ->collection('documents')
                            ->disk('public')
                            ->preserveFilenames()
                            ->downloadable()
                            ->previewable(true)
                            ->imageEditor()
                            ->maxSize(10240)  // 10MB
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/png',
                                'image/jpg',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ]),
                    ])
            ]);
    }

    private function getFileIcon(string $mimeType): string
    {
        return match (true) {
            str_contains($mimeType, 'pdf') => '/icons/pdf.png',
            str_contains($mimeType, 'word') => '/icons/doc.png',
            str_contains($mimeType, 'image') => '/icons/image.png',
            default => '/icons/file.png',
        };
    }

    private function getMimeTypeLabel(string $mimeType): string
    {
        return match (true) {
            str_contains($mimeType, 'pdf') => 'PDF',
            str_contains($mimeType, 'word') => 'Word',
            str_contains($mimeType, 'jpeg') || str_contains($mimeType, 'jpg') => 'JPEG',
            str_contains($mimeType, 'png') => 'PNG',
            default => 'File',
        };
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
