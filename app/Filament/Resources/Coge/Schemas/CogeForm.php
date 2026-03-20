<?php

namespace App\Filament\Resources\Coge\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CogeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('fonte')
                    ->required(),
                TextInput::make('entrata_uscita')
                    ->required(),
                TextInput::make('conto_avere')
                    ->required(),
                TextInput::make('descrizione_avere')
                    ->required(),
                TextInput::make('conto_dare')
                    ->required(),
                TextInput::make('descrizione_dare')
                    ->required(),
                TextInput::make('annotazioni'),
                TextInput::make('value_type')
                    ->required()
                    ->default('Quadratura'),
                Select::make('value_period')
                    ->options([
                        'Adesso' => 'Adesso',
                        'Oggi' => 'Oggi',
                        'Ieri' => 'Ieri',
                        'Settimana' => 'Settimana',
                        'Quindicinale' => 'Quindicinale',
                        'Mese' => 'Mese',
                        'Trimestre' => 'Trimestre',
                    ])
                    ->default('Oggi')
                    ->required(),
            ]);
    }
}
