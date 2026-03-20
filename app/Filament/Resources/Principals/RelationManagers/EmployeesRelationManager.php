<?php

namespace App\Filament\Resources\Principals\RelationManagers;

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

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    protected static ?string $title = 'Dipendenti Autorizzati';

    protected static ?string $modelLabel = 'Dipendente';

    protected static ?string $pluralModelLabel = 'Dipendenti';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \TextInput::make('usercode')
                    ->required()
                    ->unique()
                    ->label('Codice Utente')
                    ->helperText('Codice identificativo univoco del dipendente'),
                TextInput::make('description')
                    ->label('Descrizione')
                    ->helperText('Ruolo o note sul dipendente')
                    ->nullable(),
                DatePicker::make('start_date')
                    ->required()
                    ->label('Data Inizio')
                    ->helperText('Data di inizio autorizzazione'),
                DatePicker::make('end_date')
                    ->label('Data Fine')
                    ->helperText('Data di fine autorizzazione (lasciare vuoto per indeterminato)')
                    ->nullable(),
                Toggle::make('is_active')
                    ->label('Attivo')
                    ->default(true)
                    ->helperText('Stato attuale del dipendente'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('usercode')
            ->columns([
                TextColumn::make('usercode')
                    ->label('Codice Utente')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Codice copiato!'),
                TextColumn::make('description')
                    ->label('Descrizione')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('start_date')
                    ->label('Inizio')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Fine')
                    ->date()
                    ->sortable()
                    ->placeholder('Indeterminato'),
                IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('is_currently_active')
                    ->label('Stato Corrente')
                    ->getStateUsing(fn($record) => $record->is_currently_active ? 'Attivo' : 'Non Attivo')
                    ->badge()
                    ->color(fn($record) => $record->is_currently_active ? 'success' : 'danger'),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Stato')
                    ->options([
                        '1' => 'Attivo',
                        '0' => 'Inattivo',
                    ]),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
