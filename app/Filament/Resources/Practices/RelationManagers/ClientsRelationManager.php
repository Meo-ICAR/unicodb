<?php

namespace App\Filament\Resources\Practices\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsRelationManager extends RelationManager
{
    protected static string $relationship = 'clients';
    protected static ?string $title = 'Contraenti';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('role')
                    ->label('Ruolo')
                    ->options([
                        'intestatario' => 'Intestatario',
                        'cointestatario' => 'Co-intestatario',
                        'garante' => 'Garante',
                        'terzo_datore' => 'Terzo Datore'
                    ])
                    ->required(),
                TextInput::make('name')
                    ->label('Descrizione')
                    ->maxLength(255),
                Textarea::make('notes')
                    ->label('Note specifiche')
                    ->rows(3)
                    ->helperText('Note specifiche sul ruolo per questa pratica (es. "Garante solo per quota 50%")'),
                // --- CAMPI COMPLIANCE SPECIFICI PER QUESTA PERSONA IN QUESTA PRATICA ---
                Textarea::make('purpose_of_relationship')
                    ->label('Scopo Rapporto')
                    ->rows(2)
                    ->helperText('Es: Acquisto prima casa'),
                Textarea::make('funds_origin')
                    ->label('Origine Fondi')
                    ->rows(2)
                    ->helperText('Es: Risparmi, donazione, stipendio'),
                // Trasparenza OAM
                Toggle::make('oam_delivered')
                    ->label('Foglio Informativo Consegnato')
                    ->default(false),
                // Rischio specifico per il ruolo
                Select::make('role_risk_level')
                    ->label('Livello Rischio Ruolo')
                    ->options([
                        'basso' => 'Basso',
                        'medio' => 'Medio',
                        'alto' => 'Alto'
                    ])
                    ->helperText('Il garante potrebbe avere rischio basso, il richiedente alto'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Contraenti')
            ->modifyQueryUsing(fn($query) => $query->with('clientType'))
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('pivot.role')
                    ->label('Ruolo')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('name')
                    ->label('Nome Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('first_name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('clientType.name')
                    ->label('Tipo Cliente')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessun tipo'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telefono')
                    ->searchable()
                    ->sortable(),
                // --- COLONNE COMPLIANCE ---
                TextColumn::make('pivot.purpose_of_relationship')
                    ->label('Scopo Rapporto')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('pivot.funds_origin')
                    ->label('Origine Fondi')
                    ->limit(30)
                    ->searchable(),
                IconColumn::make('pivot.oam_delivered')
                    ->label('Foglio OAM')
                    ->boolean(),
                TextColumn::make('pivot.role_risk_level')
                    ->label('Rischio Ruolo')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'basso' => 'success',
                        'medio' => 'warning',
                        'alto' => 'danger',
                        default => 'gray'
                    }),
                TextColumn::make('pivot.notes')
                    ->label('Note')
                    ->limit(50)
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Permette di collegare un cliente esistente alla pratica
                // Permette di creare un nuovo cliente e collegarlo subito
                CreateAction::make(),
                AttachAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                // Permette di scollegare il cliente dalla pratica senza eliminarlo dal DB
                DetachAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
