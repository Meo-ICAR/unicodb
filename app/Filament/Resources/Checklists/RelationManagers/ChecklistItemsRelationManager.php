<?php

namespace App\Filament\Resources\Checklists\RelationManagers;

use App\Models\ChecklistItem;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Utilities\Get;
use Filament\Forms;
use Filament\Tables;

class ChecklistItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'checklistItems';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informazioni Elemento')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome Elemento')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('question')
                            ->label('Domanda')
                            ->required()
                            ->rows(3),
                        Textarea::make('description')
                            ->label('Descrizione')
                            ->rows(2)
                            ->nullable(),
                    ])
                    ->columns(1),
                Section::make('Configurazione')
                    ->schema([
                        Checkbox::make('is_required')
                            ->label('Obbligatorio')
                            ->default(false),
                        Checkbox::make('is_document_required')
                            ->label('Richiede Documento')
                            ->default(false),
                        Select::make('attach_model')
                            ->label('Modello Allegato')
                            ->options([
                                'principal' => 'Principal',
                                'agent' => 'Agent',
                                'company' => 'Company',
                            ])
                            ->nullable(),
                        TextInput::make('attach_model_id')
                            ->label('ID Modello')
                            ->nullable(),
                        TextInput::make('repeatable_code')
                            ->label('Codice Ripetibile')
                            ->helperText('Es: doc_annuale per documenti annuali')
                            ->nullable(),
                    ])
                    ->columns(2),
                Section::make('Logica Condizionale')
                    ->schema([
                        TextInput::make('item_code')
                            ->label('Codice Univoco')
                            ->helperText('Codice univoco della domanda per dipendenze')
                            ->nullable(),
                        TextInput::make('depends_on_code')
                            ->label('Dipende da Codice')
                            ->helperText('Il codice della domanda da cui dipende')
                            ->nullable(),
                        TextInput::make('depends_on_value')
                            ->label('Valore Dipendenza')
                            ->helperText('Il valore che deve avere per attivarsi')
                            ->nullable(),
                        Select::make('dependency_type')
                            ->label('Tipo Dipendenza')
                            ->options([
                                'show_if' => 'Mostra se',
                                'hide_if' => 'Nascondi se',
                            ])
                            ->nullable(),
                    ])
                    ->columns(2),
                Section::make('Risposta e Note')
                    ->schema([
                        Textarea::make('answer')
                            ->label('Risposta')
                            ->rows(3)
                            ->nullable(),
                        Textarea::make('annotation')
                            ->label('Annotazioni Interne')
                            ->rows(2)
                            ->nullable(),
                    ])
                    ->columns(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('question')
                    ->label('Attivita')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phase')
                    ->label('Fase')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('ordine')
                    ->label('Ordine')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
