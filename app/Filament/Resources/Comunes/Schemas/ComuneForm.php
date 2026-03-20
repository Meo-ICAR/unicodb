<?php

namespace App\Filament\Resources\Comunes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ComuneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('codice_regione')
                    ->required(),
                TextInput::make('codice_unita_territoriale')
                    ->required(),
                TextInput::make('codice_provincia_storico')
                    ->required(),
                TextInput::make('progressivo_comune')
                    ->required(),
                TextInput::make('codice_comune_alfanumerico')
                    ->required(),
                TextInput::make('denominazione')
                    ->required(),
                TextInput::make('denominazione_italiano')
                    ->required(),
                TextInput::make('denominazione_altra_lingua'),
                TextInput::make('codice_ripartizione_geografica')
                    ->required(),
                TextInput::make('ripartizione_geografica')
                    ->required(),
                TextInput::make('denominazione_regione')
                    ->required(),
                TextInput::make('denominazione_unita_territoriale')
                    ->required(),
                TextInput::make('tipologia_unita_territoriale')
                    ->required(),
                Toggle::make('capoluogo_provincia')
                    ->required(),
                TextInput::make('sigla_automobilistica')
                    ->required(),
                TextInput::make('codice_comune_numerico')
                    ->required(),
                TextInput::make('codice_comune_110_province')
                    ->required(),
                TextInput::make('codice_comune_107_province')
                    ->required(),
                TextInput::make('codice_comune_103_province')
                    ->required(),
                TextInput::make('codice_catastale')
                    ->required(),
                TextInput::make('codice_nuts1_2021')
                    ->required(),
                TextInput::make('codice_nuts2_2021')
                    ->required(),
                TextInput::make('codice_nuts3_2021')
                    ->required(),
                TextInput::make('codice_nuts1_2024')
                    ->required(),
                TextInput::make('codice_nuts2_2024')
                    ->required(),
                TextInput::make('codice_nuts3_2024')
                    ->required(),
            ]);
    }
}
