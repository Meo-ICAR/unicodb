<?php

namespace App\Filament\Resources\RuiCariches\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RuiCaricheForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('oss'),
                TextInput::make('numero_iscrizione_rui_pf'),
                TextInput::make('numero_iscrizione_rui_pg'),
                TextInput::make('qualifica_intermediario'),
                TextInput::make('responsabile'),
            ]);
    }
}
