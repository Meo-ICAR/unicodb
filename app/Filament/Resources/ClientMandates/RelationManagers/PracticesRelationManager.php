<?php

namespace App\Filament\Resources\ClientMandates\RelationManagers;

use App\Filament\Resources\Practices\PracticeResource;
use App\Models\Practice;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;

class PracticesRelationManager extends RelationManager
{
    protected static string $relationship = 'practices';

    protected static ?string $title = 'Pratiche Collegate';

    protected static ?string $modelLabel = 'Pratica';

    protected static ?string $pluralModelLabel = 'Pratiche';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome Pratica')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('practice_status_id')
                    ->label('Stato Pratica')
                    ->relationship('practiceStatus', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('practice_scope_id')
                    ->label('Ambito Pratica')
                    ->relationship('practiceScope', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('amount')
                    ->label('Importo')
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01),
                Forms\Components\Select::make('principal_id')
                    ->label('Mandante')
                    ->relationship('principal', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\Select::make('agent_id')
                    ->label('Agente')
                    ->relationship('agent', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Forms\Components\DatePicker::make('inserted_at')
                    ->label('Data Inserimento')
                    ->native(false),
                Forms\Components\Select::make('status')
                    ->label('Stato Interno')
                    ->options([
                        'working' => 'In Lavorazione',
                        'rejected' => 'Rifiutata',
                        'perfected' => 'Perfezionata',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Descrizione')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome Pratica')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('practiceStatus.name')
                    ->label('Stato Pratica')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'In Lavorazione' => 'primary',
                        'Approvata' => 'success',
                        'Rifiutata' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('practiceScope.name')
                    ->label('Ambito')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Importo')
                    ->money('EUR')
                    ->sortable()
                    ->summarize(
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('EUR')
                            ->label('Totale')
                    ),
                Tables\Columns\TextColumn::make('principal.name')
                    ->label('Mandante')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('agent.name')
                    ->label('Agente')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Stato Interno')
                    ->colors([
                        'working' => 'primary',
                        'rejected' => 'danger',
                        'perfected' => 'success',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'working' => 'In Lavorazione',
                        'rejected' => 'Rifiutata',
                        'perfected' => 'Perfezionata',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('inserted_at')
                    ->label('Data Inserimento')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attiva')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('practice_status_id')
                    ->label('Stato Pratica')
                    ->relationship('practiceStatus', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('practice_scope_id')
                    ->label('Ambito Pratica')
                    ->relationship('practiceScope', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Stato Interno')
                    ->options([
                        'working' => 'In Lavorazione',
                        'rejected' => 'Rifiutata',
                        'perfected' => 'Perfezionata',
                    ]),
                Tables\Filters\SelectFilter::make('principal_id')
                    ->label('Mandante')
                    ->relationship('principal', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('amount_range')
                    ->label('Range Importo')
                    ->form([
                        Forms\Components\TextInput::make('min')
                            ->label('Importo Minimo')
                            ->numeric()
                            ->prefix('€'),
                        Forms\Components\TextInput::make('max')
                            ->label('Importo Massimo')
                            ->numeric()
                            ->prefix('€'),
                    ])
                    ->query(function (Tables\Filter $query, array $data): Tables\Filter {
                        return $query
                            ->when(
                                $data['min'],
                                fn(Tables\Filter $query, $amount): Tables\Filter => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['max'],
                                fn(Tables\Filter $query, $amount): Tables\Filter => $query->where('amount', '<=', $amount),
                            );
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
