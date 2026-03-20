<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
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

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informazioni Generali')
                    ->schema([
                        Select::make('document_type_id')
                            ->relationship('documentType', 'name')
                            ->label('Tipo Documento')
                            ->required(),
                        Select::make('document_status_id')
                            ->relationship('documentStatus', 'name')
                            ->label('Stato Documento')
                            ->nullable()
                            ->helperText('Stato di validità del documento'),
                        TextInput::make('name')
                            ->label('Nome Documento')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Descrizione')
                            ->rows(3)
                            ->nullable(),
                    ])
                    ->columns(2),
                Section::make('Dettagli Documento')
                    ->schema([
                        TextInput::make('docnumber')
                            ->label('Numero Documento')
                            ->nullable(),
                        TextInput::make('emitted_by')
                            ->label('Ente Rilascio')
                            ->nullable(),
                        DatePicker::make('emitted_at')
                            ->label('Data Emissione')
                            ->nullable(),
                        DatePicker::make('expires_at')
                            ->label('Data Scadenza')
                            ->nullable(),
                    ])
                    ->columns(2),
                Section::make('Stato e Validazione')
                    ->schema([
                        Toggle::make('is_signed')
                            ->label('Firmato')
                            ->default(false),
                        Toggle::make('is_template')
                            ->label('Template Fornito')
                            ->default(false)
                            ->helperText('Indica se forniamo noi il documento'),
                        TextInput::make('status')
                            ->label('Stato Interno')
                            ->default('uploaded')
                            ->helperText('Stato di caricamento del documento'),
                        DateTimePicker::make('verified_at')
                            ->label('Data Verifica')
                            ->nullable()
                            ->disabled(),
                        Select::make('verified_by')
                            ->relationship('verifiedBy', 'name')
                            ->label('Verificato Da')
                            ->nullable()
                            ->disabled(),
                    ])
                    ->columns(2),
                Section::make('Note e Annotazioni')
                    ->schema([
                        Textarea::make('annotation')
                            ->label('Annotazioni Interne')
                            ->rows(3)
                            ->nullable(),
                        Textarea::make('rejection_note')
                            ->label('Note Rifiuto')
                            ->rows(3)
                            ->nullable()
                            ->helperText('Motivazioni del rifiuto del documento'),
                    ])
                    ->columns(1),
                Section::make('Upload Documento')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('document_file')
                            ->label('Carica Documento')
                            ->collection('documents')
                            ->multiple(false)
                            ->maxSize(10240)  // 10MB
                            ->acceptedFileTypes(['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'])
                            ->helperText('Formati accettati: PDF, DOC, DOCX, JPG, JPEG, PNG (max 10MB)'),
                    ])
                    ->columns(1),
            ]);
    }
}
