<?php

namespace App\Filament\Resources\OamScopes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OamScopeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('Codice OAM'),
                TextInput::make('name')
                    ->required()
                    ->label('Descrizione Ambito'),
            ]);
    }
}
