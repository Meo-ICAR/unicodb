<?php

namespace App\Filament\Resources\Companies\Schemas;

use App\Filament\RelationManagers\DocumentsRelationManager;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
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

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    // Colonna sinistra - Informazioni principali
                    Section::make('Informazioni Azienda')
                        ->schema([
                            TextInput::make('name')
                                ->required(),
                            TextInput::make('vat_number')
                                ->label('CF / Partita IVA'),
                        ]),
                    // Colonna destra - Dettagli e Brand
                    Section::make('Dettagli')
                        ->description('Ulteriori dettagli azienda')
                        ->collapsed()
                        ->schema([
                            TextInput::make('vat_name'),
                            TextInput::make('oam'),
                            DatePicker::make('oam_at'),
                            TextInput::make('oam_name'),
                            Select::make('company_type_id')
                                ->relationship('companyType', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                        ]),
                    Section::make('---')
                        ->schema([]),
                    Section::make('Brand e Documentazione')
                        ->description('Logo aziendale e intestazione carta intestata')
                        ->collapsed()
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('logo')
                                ->label('Logo Azienda')
                                ->image()
                                ->imageEditor()
                                ->directory('companies/logos')
                                ->visibility('public')
                                ->collection('logo')
                                ->maxSize(2048)
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/svg+xml', 'image/webp'])
                                ->helperText("Carica il logo dell'azienda (max 2MB, formati: JPG, PNG, SVG, WebP)"),
                            RichEditor::make('page_header')
                                ->label('Intestazione Carta Intestata')
                                ->helperText("Testo che apparirà nell'intestazione dei documenti ufficiali")
                                ->columnSpanFull(),
                            RichEditor::make('page_footer')
                                ->label('Piè di Pagina Carta Intestata')
                                ->helperText('Testo che apparirà nel piè di pagina dei documenti ufficiali')
                                ->columnSpanFull(),
                        ]),
                    Section::make('Configurazione SMTP')
                        ->description('Impostazioni server email per invio comunicazioni')
                        ->collapsed()
                        ->schema([
                            Toggle::make('smtp_enabled')
                                ->label('Abilita Invio SMTP')
                                ->helperText('Attiva invio email tramite server SMTP personalizzato')
                                ->default(false),
                            Grid::make(2)->schema([
                                TextInput::make('smtp_host')
                                    ->label('Host SMTP')
                                    ->placeholder('smtp.esempio.com')
                                    ->helperText('Server SMTP per invio email'),
                                TextInput::make('smtp_port')
                                    ->label('Porta SMTP')
                                    ->numeric()
                                    ->default(587)
                                    ->helperText('Porta server SMTP (solitamente 587 per TLS, 465 per SSL)'),
                            ]),
                            Grid::make(2)->schema([
                                TextInput::make('smtp_username')
                                    ->label('Username SMTP')
                                    ->helperText('Nome utente per autenticazione SMTP'),
                                TextInput::make('smtp_password')
                                    ->label('Password SMTP')
                                    ->password()
                                    ->helperText('Password per autenticazione SMTP'),
                            ]),
                            Grid::make(2)->schema([
                                Select::make('smtp_encryption')
                                    ->label('Crittografia')
                                    ->options([
                                        'tls' => 'TLS',
                                        'ssl' => 'SSL',
                                        '' => 'Nessuna',
                                    ])
                                    ->default('tls')
                                    ->helperText('Tipo di crittografia della connessione'),
                                Toggle::make('smtp_verify_ssl')
                                    ->label('Verifica SSL')
                                    ->default(true)
                                    ->helperText('Verifica certificato SSL del server'),
                            ]),
                            Grid::make(2)->schema([
                                TextInput::make('smtp_from_email')
                                    ->label('Email Mittente')
                                    ->email()
                                    ->helperText('Email da cui verranno inviate le comunicazioni'),
                                TextInput::make('smtp_from_name')
                                    ->label('Nome Mittente')
                                    ->helperText('Nome visualizzato come mittente delle email'),
                            ]),
                        ]),
                ]),
            ]);
    }
}
