<?php

namespace App\Filament\Resources\Agents\Schemas;

use App\Model\Oam;
use App\Services\ChecklistService;
use App\Services\GeminiVisionService;
use App\Traits\HasDocumentTypeFiltering;
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

class AgentForm
{
    use HasDocumentTypeFiltering;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. SEZIONE ANAGRAFICA E STATUS
                Section::make('Anagrafica e Inquadramento')
                    ->collapsible()
                    ->collapsed()
                    ->description('Dati principali e collegamento utente del collaboratore.')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nome / Denominazione')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2)
                                ->live(debounce: 300)
                                ->afterStateUpdated(function (string $state, callable $set) {
                                    if (strlen($state) < 2) {
                                        return;
                                    }

                                    $suggestions = \App\Models\Oam::where('name', 'LIKE', '%' . $state . '%')
                                        ->limit(5)
                                        ->pluck('name')
                                        ->toArray();

                                    if (!empty($suggestions)) {
                                        // Notifica l'utente con i suggerimenti
                                        \Filament\Notifications\Notification::make()
                                            ->title('Suggerimenti OAM')
                                            ->body('Nomi trovati: ' . implode(', ', $suggestions))
                                            ->info()
                                            ->send();
                                    }
                                }),
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->maxLength(100),
                            TextInput::make('phone')
                                ->label('Telefono')
                                ->tel()
                                ->maxLength(20),
                            Select::make('type')
                                ->label('Tipologia Collaboratore')
                                ->options([
                                    'Agente' => 'Agente',
                                    'Mediatore' => 'Mediatore',
                                    'Consulente' => 'Consulente',
                                    'Call Center' => 'Call Center',
                                ])
                                ->searchable(),
                            Select::make('supervisor_type')
                                ->label('Tipo Supervisore')
                                ->options([
                                    'no' => 'No',
                                    'si' => 'Sì',
                                    'filiale' => 'Filiale',
                                ])
                                ->default('no')
                                ->helperText('Se supervisore indicare e specificare se di filiale'),
                            Toggle::make('is_active')
                                ->label('Attivo/Convenzionato')
                                ->default(true)
                                ->inline(false),
                            Toggle::make('is_art108')
                                ->label('Esente Art. 108')
                                ->default(false)
                                ->inline(false)
                                ->helperText('Esente art. 108 - ex art. 128-novies TUB'),
                            Select::make('user_id')
                                ->label('Utente di Sistema Collegato')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->helperText("Associa questo profilo a un account per l'accesso al CRM."),
                            // company_id solitamente si gestisce in background col multi-tenancy,
                            // ma se serve selezionarlo a mano:
                            Select::make('company_branch_id')
                                ->label('Filiale di Riferimento')
                                ->relationship('companyBranch', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable()
                                ->helperText('Filiale specifica di riferimento per questo agente'),
                        ]),
                        Textarea::make('description')
                            ->label('Note / Descrizione interna')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                // 2. SEZIONE NORMATIVA E OAM
                Section::make('Dati OAM e Mandato')
                    ->collapsible()
                    ->collapsed()
                    ->description("Estremi di iscrizione all'elenco e date di validità del contratto.")
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('numero_iscrizione_rui')
                                ->label('Numero Iscrizione OAM')
                                ->maxLength(30)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $state, callable $set, callable $get) {
                                    if (empty($state)) {
                                        $set('oam_name', null);
                                        $set('oam_at', null);
                                        return;
                                    }

                                    $oam = \App\Models\Oam::where('numero_iscrizione_rui', $state)->first();

                                    if ($oam) {
                                        $set('oam_name', $oam->name);
                                        $set('oam_at', $oam->data_iscrizione);
                                    } else {
                                        $set('oam_name', null);
                                        $set('oam_at', null);
                                    }
                                }),
                            TextInput::make('oam')
                                ->label('Codice OAM')
                                ->maxLength(255),
                            TextInput::make('oam_name')
                                ->label('Denominazione registrata in OAM')
                                ->maxLength(255),
                            DatePicker::make('oam_at')
                                ->label('Data Iscrizione OAM')
                                ->displayFormat('d/m/Y'),
                            TextInput::make('ivass')
                                ->label('Codice Iscrizione IVASS')
                                ->maxLength(30),
                            TextInput::make('ivass_name')
                                ->label('Denominazione IVASS')
                                ->maxLength(255),
                            Select::make('ivass_section')
                                ->label('Sezione IVASS')
                                ->options([
                                    'A' => 'Sezione A',
                                    'B' => 'Sezione B',
                                    'C' => 'Sezione C',
                                    'D' => 'Sezione D',
                                    'E' => 'Sezione E',
                                ])
                                ->nullable(),
                            DatePicker::make('ivass_at')
                                ->label('Data Iscrizione IVASS')
                                ->displayFormat('d/m/Y'),
                            SpatieMediaLibraryFileUpload::make('identity_document')
                                ->collection('identity_documents')
                                ->label('Documento di Identità'),
                            // TextInput::make('nome'),
                            // TextInput::make('cognome'),
                            // TextInput::make('numero_documento'),
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('stipulated_at')
                                ->label('Data Inizio Mandato')
                                ->displayFormat('d/m/Y')
                                ->required(),
                            DatePicker::make('dismissed_at')
                                ->label('Data Cessazione Rapporto')
                                ->displayFormat('d/m/Y')
                                ->helperText('Compilare solo in caso di interruzione del rapporto.'),
                        ]),
                    ]),
                // 3. SEZIONE FISCALE ED ENASARCO
                Section::make('Fiscale ed Enasarco')
                    ->collapsible()
                    ->collapsed()
                    ->description('Dati per la fatturazione e inquadramento previdenziale.')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('vat_name')
                                ->label('Ragione Sociale Fiscale')
                                ->maxLength(255),
                            TextInput::make('vat_number')
                                ->label('CF / Partita IVA')
                                ->maxLength(16),
                            Select::make('enasarco')
                                ->label('Posizione Enasarco')
                                ->options([
                                    'no' => 'Non soggetto',
                                    'monomandatario' => 'Monomandatario',
                                    'plurimandatario' => 'Plurimandatario',
                                    'societa' => 'Società di Capitali',
                                ])
                                ->default('no'),
                            TextInput::make('contoCOGE')
                                ->label('Conto COGE (Contabilità)')
                                ->maxLength(255)
                                ->helperText("Codice conto per l'esportazione in contabilità."),
                        ]),
                    ]),
                // 4. SEZIONE CONTRIBUTI E RIMBORSI
                Section::make('Condizioni Economiche Fisse')
                    ->collapsible()
                    ->collapsed()
                    ->description('Fee fisse mensili, rimborsi e addebiti ricorrenti (Desk, CRM, ecc.).')
                    ->icon('heroicon-o-currency-euro')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('contribute')
                                ->label('Addebito Fisso (Desk/CRM)')
                                ->numeric()
                                ->prefix('€')
                                ->maxValue(99999999.99),
                            TextInput::make('contributeFrequency')
                                ->label('Frequenza addebito (Mesi)')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->maxValue(12),
                            DatePicker::make('contributeFrom')
                                ->label('Inizio addebito dal')
                                ->displayFormat('d/m/Y'),
                            TextInput::make('remburse')
                                ->label('Rimborso Spese Fisso Mensile')
                                ->numeric()
                                ->prefix('€')
                                ->maxValue(99999999.99)
                                ->columnSpan(3),
                        ]),
                    ]),
            ]);
    }
}
