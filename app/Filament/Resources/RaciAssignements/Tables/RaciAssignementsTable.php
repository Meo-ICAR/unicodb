<?php

namespace App\Filament\Resources\RaciAssignements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Filter;
use Filament\Tables\Columns\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Forms\Components\Repeater;

class RaciAssignementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scope.name')
                    ->label('Prodotto')
                    ->badge()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Attività')
                    ->searchable(),
                // Visualizzazione compatta della matrice
                TextColumn::make('businessFunctions.code')
                    ->label('Matrice (Ruolo: Codice)')
                    ->badge()
                    ->formatStateUsing(fn($state, $record) =>
                        $record->businessFunctions->map(fn($f) => "{$f->pivot->role}: {$f->code}")->implode(' | '))
                    ->color(fn($state) => str_contains($state, 'A:') ? 'danger' : 'gray'),
            ])
            ->filters([
                //  SelectFilter::make('scope')
                //      ->relationship('scope', 'name')
            ]);
        // 4. Validazione Critica (Business Logic)
        // Un mediatore non può avere due Accountable (A) per lo stesso task. In Filament 5.2 puoi aggiungere un ValidationRule nel form:

        /*
         * ->rules([
         *     fn() => function (string $attribute, $value, \Closure $fail) {
         *         $accountables = collect($value)->where('role', 'A')->count();
         *         if ($accountables !== 1) {
         *             $fail('Deve esserci esattamente un Accountable (A) per ogni task.');
         *         }
         *     },
         * ]);
         */
    }
}
