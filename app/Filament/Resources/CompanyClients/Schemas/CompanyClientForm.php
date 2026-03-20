<?php

namespace App\Filament\Resources\CompanyClients\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CompanyClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->relationship('client', 'name')
                    ->required(),
                TextInput::make('role')
                    ->required()
                    ->default('privacy'),
                TextInput::make('privacy_role'),
                Textarea::make('purpose')
                    ->columnSpanFull(),
                Textarea::make('data_subjects')
                    ->columnSpanFull(),
                Textarea::make('data_categories')
                    ->columnSpanFull(),
                TextInput::make('retention_period'),
                TextInput::make('extra_eu_transfer'),
                Textarea::make('security_measures')
                    ->columnSpanFull(),
                TextInput::make('privacy_data'),
            ]);
    }
}
