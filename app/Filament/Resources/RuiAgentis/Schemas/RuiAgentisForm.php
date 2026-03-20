<?php

namespace App\Filament\Resources\RuiAgentis\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RuiAgentisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('numero_iscrizione_d'),
                TextInput::make('numero_iscrizione_a'),
                DateTimePicker::make('data_conferimento'),
                TextInput::make('codice_compagnia'),
                TextInput::make('ragione_sociale'),
            ]);
    }
}
