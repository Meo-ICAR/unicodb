<?php

namespace App\Filament\Resources\RuiSezds\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RuiSezdsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('numero_iscrizione_d'),
                TextInput::make('ragione_sociale'),
                TextInput::make('cognome_nome_responsabile'),
            ]);
    }
}
