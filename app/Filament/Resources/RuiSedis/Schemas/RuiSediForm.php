<?php

namespace App\Filament\Resources\RuiSedis\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RuiSediForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('oss'),
                TextInput::make('numero_iscrizione_int'),
                TextInput::make('tipo_sede'),
                TextInput::make('comune_sede'),
                TextInput::make('provincia_sede'),
                TextInput::make('cap_sede'),
                TextInput::make('indirizzo_sede'),
            ]);
    }
}
