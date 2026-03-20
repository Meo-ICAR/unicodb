<?php

namespace App\Filament\Resources\PracticeCommissions\Schemas;

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

class PracticeCommissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SEZIONE PRINCIPALE
                Section::make('Informazioni Generali')
                    ->description('Dati principali della provvigione')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('practice_id')
                                ->label('Pratica')
                                ->relationship('practice', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('proforma_id')
                                ->label('Proforma')
                                ->relationship('proforma', 'name')
                                ->searchable()
                                ->preload()
                                ->helperText('Proforma di liquidazione'),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('agent_id')
                                ->label('Agente')
                                ->relationship('agent', 'name')
                                ->searchable()
                                ->preload(),
                            Select::make('principal_id')
                                ->label('Mandante')
                                ->relationship('principal', 'name')
                                ->searchable()
                                ->preload(),
                        ]),
                        Select::make('practice_commission_status_id')
                            ->label('Stato Commissione')
                            ->relationship('practiceCommissionStatus', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Seleziona lo stato della commissione'),
                        TextInput::make('CRM_code')
                            ->label('Codice CRM')
                            ->maxLength(255),
                        DatePicker::make('inserted_at')
                            ->label('Data Inserimento'),
                    ]),
                // SEZIONE DETTAGLIO PROVVIGIONE
                Section::make('Dettaglio Provvigione')
                    ->description('Tipo e importo della commissione')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Descrizione Provvigione')
                                ->maxLength(255)
                                ->helperText('Es. Bonus extra o Provvigione base'),
                            TextInput::make('amount')
                                ->label('Importo Lordo')
                                ->numeric()
                                ->prefix('€')
                                ->step(0.01)
                                ->helperText('Importo provvigionale lordo per questa singola pratica'),
                        ]),
                        TextInput::make('description')
                            ->label('Note')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                // SEZIONE TIPOLOGIA
                Section::make('Tipologia Provvigione')
                    ->description('Caratteristiche della commissione')
                    ->schema([
                        Grid::make(3)->schema([
                            Toggle::make('is_enasarco')
                                ->label('ENASARCO')
                                ->default(true)
                                ->helperText('Provvigione da conteggiare per ENASARCO'),
                            Toggle::make('is_insurance')
                                ->label('Assicurativa')
                                ->default(true)
                                ->helperText('Provvigione assicurativa'),
                            Toggle::make('is_payment')
                                ->label('Passiva Rete')
                                ->helperText('Provvigione passiva verso rete'),
                        ]),
                        Grid::make(3)->schema([
                            Toggle::make('is_recurrent')
                                ->label('Ricorrente')
                                ->helperText('Compenso ricorrente'),
                            Toggle::make('is_prize')
                                ->label('Premio Mandante')
                                ->helperText('Premio da mandante'),
                            Toggle::make('is_client')
                                ->label('Da Cliente')
                                ->helperText('Compenso da cliente'),
                        ]),
                        Toggle::make('is_coordination')
                            ->label('Coordinamento')
                            ->default(false)
                            ->helperText('Compenso coordinamento'),
                    ]),
                // SEZIONE STATI E DATE
                Section::make('Stati e Date')
                    ->description('Stato pagamento e date importanti')
                    ->schema([
                        Grid::make(2)->schema([
                            DatePicker::make('perfected_at')
                                ->label('Data Perfezionamento')
                                ->helperText('Data perfezionamento provvigione'),
                            DatePicker::make('cancellation_at')
                                ->label('Data Annullamento')
                                ->helperText('Data annullamento provvigione'),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('invoice_number')
                                ->label('Numero Fattura')
                                ->maxLength(30),
                            DatePicker::make('invoice_at')
                                ->label('Data Fattura'),
                        ]),
                        DatePicker::make('paided_at')
                            ->label('Data Pagamento'),
                        DatePicker::make('status_at')
                            ->label('Data Stato Pagamento'),
                    ]),
                // SEZIONE STORNO
                Section::make('Storno')
                    ->description('Gestione storno provvigionale')
                    ->schema([
                        Toggle::make('is_storno')
                            ->label('Storno Provvigionale')
                            ->reactive()
                            ->helperText('Attiva per stornare questa provvigione'),
                        Grid::make(2)->schema([
                            DatePicker::make('storned_at')
                                ->label('Data Storno')
                                ->visible(fn(callable $get) => $get('is_storno')),
                            TextInput::make('storno_amount')
                                ->label('Importo Stornato')
                                ->numeric()
                                ->prefix('€')
                                ->step(0.01)
                                ->visible(fn(callable $get) => $get('is_storno'))
                                ->helperText('Importo provvigionale stornato'),
                        ]),
                    ]),
            ]);
    }
}
