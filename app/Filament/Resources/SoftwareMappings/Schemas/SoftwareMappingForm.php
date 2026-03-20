<?php

namespace App\Filament\Resources\SoftwareMappings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SoftwareMappingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('mapping_type')
                    ->options([
            'PRACTICE_TYPE' => 'P r a c t i c e  t y p e',
            'PRACTICE_STATUS' => 'P r a c t i c e  s t a t u s',
            'CLIENT_TYPE' => 'C l i e n t  t y p e',
            'BANK_NAME' => 'B a n k  n a m e',
        ])
                    ->required(),
                TextInput::make('name'),
                TextInput::make('external_value')
                    ->required(),
                TextInput::make('internal_id')
                    ->required()
                    ->numeric(),
                TextInput::make('description'),
            ]);
    }
}
