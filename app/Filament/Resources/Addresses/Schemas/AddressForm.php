<?php

namespace App\Filament\Resources\Addresses\Schemas;

use App\Models\AddressType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AddressForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('address_type_id')
                    ->label('Tipo Indirizzo')
                    ->options(AddressType::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('street')
                    ->label('Via')
                    ->required()
                    ->maxLength(255),
                TextInput::make('city')
                    ->label('Città')
                    ->required()
                    ->maxLength(100),
                TextInput::make('zip_code')
                    ->label('CAP')
                    ->maxLength(5),
            ]);
    }
}
