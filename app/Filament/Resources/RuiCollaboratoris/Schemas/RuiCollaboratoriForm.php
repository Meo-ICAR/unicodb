<?php

namespace App\Filament\Resources\RuiCollaboratoris\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RuiCollaboratoriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('oss'),
                TextInput::make('livello'),
                TextInput::make('num_iscr_intermediario'),
                TextInput::make('num_iscr_collaboratori_i_liv'),
                TextInput::make('num_iscr_collaboratori_ii_liv'),
                TextInput::make('qualifica_rapporto'),
            ]);
    }
}
