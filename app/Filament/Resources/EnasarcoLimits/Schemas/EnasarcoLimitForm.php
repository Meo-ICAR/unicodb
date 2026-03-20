<?php

namespace App\Filament\Resources\EnasarcoLimits\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EnasarcoLimitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('year')
                    ->required()
                    ->numeric(),
                TextInput::make('minimal_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('maximal_amount')
                    ->required()
                    ->numeric(),
            ]);
    }
}
