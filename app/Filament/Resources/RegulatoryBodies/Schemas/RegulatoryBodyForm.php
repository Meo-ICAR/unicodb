<?php

namespace App\Filament\Resources\RegulatoryBodies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class RegulatoryBodyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('acronym'),
                TextInput::make('official_website')
                    ->url(),
                TextInput::make('pec_address'),
                TextInput::make('portal_url')
                    ->url(),
                TextInput::make('contact_person'),
                TextInput::make('phone_support')
                    ->tel(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
