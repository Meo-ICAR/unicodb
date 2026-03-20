<?php

namespace App\Filament\Resources\RuiMandatis\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RuiMandatiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('oss'),
                TextInput::make('matricola'),
                TextInput::make('codice_compagnia'),
                TextInput::make('ragione_sociale'),
            ]);
    }
}
