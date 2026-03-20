<?php

namespace App\Filament\Resources\PrincipalContacts\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PrincipalContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('role_title'),
                TextInput::make('department'),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                TextInput::make('phone_office')
                    ->tel(),
                TextInput::make('phone_mobile')
                    ->tel(),
                Toggle::make('is_active'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
