<?php

namespace App\Filament\Resources\CompanyBranches\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyBranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni Sede')
                    ->description('Dati principali della sede aziendale')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome Sede')
                            ->required()
                            ->maxLength(255),
                        Toggle::make('is_main_office')
                            ->label('Sede Principale')
                            ->required(),
                        TextInput::make('manager_first_name')
                            ->label('Nome Responsabile')
                            ->maxLength(100),
                        TextInput::make('manager_last_name')
                            ->label('Cognome Responsabile')
                            ->maxLength(100),
                        TextInput::make('manager_tax_code')
                            ->label('Codice Fiscale Responsabile')
                            ->maxLength(16),
                    ]),
                Section::make('Indirizzo')
                    ->description('Indirizzo della sede')
                    ->schema([
                        TextInput::make('address.name')
                            ->label('Nome Indirizzo')
                            ->maxLength(255),
                        TextInput::make('address.street')
                            ->label('Via')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('address.city')
                            ->label('Città')
                            ->required()
                            ->maxLength(100),
                        TextInput::make('address.zip_code')
                            ->label('CAP')
                            ->required()
                            ->maxLength(10),
                        Select::make('address.address_type_id')
                            ->label('Tipo Indirizzo')
                            ->options(\App\Models\AddressType::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ]),
            ]);
    }
}
