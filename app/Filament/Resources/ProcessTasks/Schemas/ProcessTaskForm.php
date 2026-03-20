<?php

namespace App\Filament\Resources\ProcessTasks\Schemas;

use Filament\Schemas\Schema;

class ProcessTaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Section::make('Definizione Attività')
                    ->description("Collega il task al prodotto e definisci l'ordine di esecuzione.")
                    ->schema([
                        Forms\Components\Select::make('practice_scope_id')
                            ->label('Ambito Pratica (Prodotto)')
                            ->relationship('scope', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nome Task')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordine Sequenza')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),
                Forms\Components\Section::make('Matrice RACI')
                    ->description('Assegna le responsabilità alle funzioni business.')
                    ->schema([
                        Forms\Components\Repeater::make('raciAssignments')
                            ->relationship()  // Laravel 12 + Filament 5.2 gestiscono la pivot automaticamente
                            ->schema([
                                Forms\Components\Select::make('business_function_id')
                                    ->label('Funzione Business')
                                    ->relationship('businessFunction', 'name')
                                    ->required()
                                    ->distinct()  // Impedisce di selezionare la stessa funzione due volte nel repeater
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                Forms\Components\Select::make('role')
                                    ->label('Ruolo')
                                    ->options([
                                        'R' => 'Responsible (Chi esegue)',
                                        'A' => 'Accountable (Chi approva)',
                                        'C' => 'Consulted (Chi supporta)',
                                        'I' => 'Informed (Chi osserva)',
                                    ])
                                    ->required()
                                    ->native(false),
                            ])
                            ->columns(2)
                            ->itemLabel(fn(array $state): ?string =>
                                $state['role'] ?? 'Nuova assegnazione')
                            ->collapsible()
                            ->cloneable()  // Feature comoda per duplicare ruoli velocemente
                    ]),
            ]);
    }
}
