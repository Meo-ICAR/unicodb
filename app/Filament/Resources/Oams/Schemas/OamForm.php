<?php

namespace App\Filament\Resources\Oams\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('autorizzato_ad_operare')
                    ->required(),
                TextInput::make('persona')
                    ->required(),
                TextInput::make('codice_fiscale')
                    ->required(),
                TextInput::make('domicilio_sede_legale')
                    ->required(),
                TextInput::make('elenco')
                    ->required(),
                TextInput::make('numero_iscrizione')
                    ->required(),
                DatePicker::make('data_iscrizione'),
                TextInput::make('stato')
                    ->required(),
                DatePicker::make('data_stato'),
                Textarea::make('causale_stato_note')
                    ->columnSpanFull(),
            ]);
    }
}
