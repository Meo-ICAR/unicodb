<?php

namespace App\Filament\Resources\Ruis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RuiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('oss'),
                Toggle::make('inoperativo'),
                DatePicker::make('data_inizio_inoperativita'),
                TextInput::make('numero_iscrizione_rui'),
                DatePicker::make('data_iscrizione'),
                TextInput::make('cognome_nome'),
                TextInput::make('stato'),
                TextInput::make('comune_nascita'),
                DatePicker::make('data_nascita'),
                TextInput::make('ragione_sociale'),
                TextInput::make('provincia_nascita'),
                TextInput::make('titolo_individuale_sez_a'),
                TextInput::make('attivita_esercitata_sez_a'),
                TextInput::make('titolo_individuale_sez_b'),
                TextInput::make('attivita_esercitata_sez_b'),
                TextInput::make('rui_section_id')
                    ->numeric(),
            ]);
    }
}
