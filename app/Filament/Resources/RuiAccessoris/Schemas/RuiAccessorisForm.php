<?php

namespace App\Filament\Resources\RuiAccessoris\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RuiAccessorisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('numero_iscrizione_e'),
                TextInput::make('ragione_sociale'),
                TextInput::make('cognome_nome'),
                TextInput::make('sede_legale'),
                DatePicker::make('data_nascita'),
                TextInput::make('luogo_nascita'),
            ]);
    }
}
