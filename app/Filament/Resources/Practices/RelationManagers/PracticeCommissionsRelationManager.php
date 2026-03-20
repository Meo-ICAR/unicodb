<?php

namespace App\Filament\Resources\Practices\RelationManagers;

use App\Models\PracticeCommission;
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
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PracticeCommissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'practiceCommissions';

    protected static ?string $title = 'Commissioni Pratica';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('practice_commissions.company_id', auth()->user()->company_id))
            ->recordTitleAttribute('amount')
            ->columns([
                TextColumn::make('agent.name')
                    ->label('Agent')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Importo')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('percentage')
                    ->label('Percentuale')
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PENDING' => 'warning',
                        'APPROVED' => 'success',
                        'REJECTED' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('notes')
                    ->label('Note')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('created_at')
                    ->label('Data Creazione')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Data Aggiornamento')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Stato')
                    ->options([
                        'PENDING' => 'In Attesa',
                        'APPROVED' => 'Approvata',
                        'REJECTED' => 'Rifiutata',
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nuova Commissione'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Modifica'),
                DeleteAction::make()
                    ->label('Elimina'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Elimina Selezionati'),
                ]),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('agent_id')
                    ->label('Agent')
                    ->relationship('agent', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('amount')
                    ->label('Importo')
                    ->numeric()
                    ->prefix('€')
                    ->required()
                    ->step(0.01),
                TextInput::make('percentage')
                    ->label('Percentuale')
                    ->numeric()
                    ->suffix('%'),
                Select::make('status')
                    ->label('Stato')
                    ->options([
                        'PENDING' => 'In Attesa',
                        'APPROVED' => 'Approvata',
                        'REJECTED' => 'Rifiutata',
                    ])
                    ->default('PENDING')
                    ->required(),
                Textarea::make('notes')
                    ->label('Note')
                    ->rows(3),
            ]);
    }
}
