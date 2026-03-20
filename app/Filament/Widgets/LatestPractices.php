<?php

namespace App\Filament\Widgets;

use App\Models\Practice;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestPractices extends TableWidget
{
    //  protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected function getHeading(): string
    {
        return 'Ultime Pratiche';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Practice::query())
            ->columns([
                TextColumn::make('practiceScope.name')
                    ->label('Ambito')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('principal.name')
                    ->label('Principal')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('perfected_at')
                    ->date()
                    ->sortable()
                    ->label('Perfezionata'),
                TextColumn::make('agent.name')
                    ->label('Agente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable()
                    ->label('Importo')
                    ->formatStateUsing(fn($state) => '€ ' . number_format($state, 2, ',', '.')),
                TextColumn::make('net')
                    ->numeric()
                    ->sortable()
                    ->label('Netto')
                    ->formatStateUsing(fn($state) => '€ ' . number_format($state, 2, ',', '.')),
                TextColumn::make('name')
                    ->label('Pratica')
                    ->searchable(),
                TextColumn::make('CRM_code')
                    ->label('Codice CRM')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('principal_code')
                    ->label('Codice Principal')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Attiva')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Creata')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Aggiornata')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
