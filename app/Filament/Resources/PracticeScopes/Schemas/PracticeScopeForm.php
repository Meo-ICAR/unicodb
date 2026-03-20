<?php

namespace App\Filament\Resources\PracticeScopes\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PracticeScopeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Nome Scope'),
                TextInput::make('code')
                    ->required()
                    ->label('Codice')
                    ->maxLength(20),
                TextInput::make('oam_code')
                    ->label('Codice OAM')
                    ->maxLength(255),
                Checkbox::make('is_oneclient')
                    ->label('Finanziamento Mono Cliente')
                    ->default(true)
                    ->helperText('Se true, indica che questo scope è per finanziamenti mono-cliente'),
            ]);
    }
}
