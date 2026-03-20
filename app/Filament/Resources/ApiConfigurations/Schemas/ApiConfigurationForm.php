<?php

namespace App\Filament\Resources\ApiConfigurations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ApiConfigurationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
                TextInput::make('base_url')
                    ->url(),
                Select::make('auth_type')
                    ->options([
            'BASIC' => 'B a s i c',
            'BEARER_TOKEN' => 'B e a r e r  t o k e n',
            'API_KEY' => 'A p i  k e y',
            'OAUTH2' => 'O a u t h2',
        ])
                    ->default('API_KEY'),
                Textarea::make('api_key')
                    ->columnSpanFull(),
                Textarea::make('api_secret')
                    ->columnSpanFull(),
                DateTimePicker::make('token_expires_at'),
                Toggle::make('is_active'),
                TextInput::make('webhook_secret'),
                DateTimePicker::make('last_sync_at'),
            ]);
    }
}
