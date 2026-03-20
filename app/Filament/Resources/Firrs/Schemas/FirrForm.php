<?php

namespace App\Filament\Resources\Firrs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FirrForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('minimo')
                    ->numeric(),
                TextInput::make('massimo')
                    ->numeric(),
                TextInput::make('aliquota')
                    ->numeric(),
                TextInput::make('competenza')
                    ->required()
                    ->numeric()
                    ->default(2025),
                Select::make('enasarco')
                    ->options([
            'monomandatario' => 'Monomandatario',
            'plurimandatario' => 'Plurimandatario',
            'societa' => 'Societa',
            'no' => 'No',
        ])
                    ->default('plurimandatario')
                    ->required(),
            ]);
    }
}
