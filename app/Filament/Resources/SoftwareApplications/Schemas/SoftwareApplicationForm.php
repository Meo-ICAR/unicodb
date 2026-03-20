<?php

namespace App\Filament\Resources\SoftwareApplications\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SoftwareApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni Generali')
                    ->schema([
                        Select::make('category_id')
                            ->label('Categoria Software')
                            ->relationship('softwareCategory', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Nome Software')
                            ->required(),
                        TextInput::make('provider_name')
                            ->label('Provider/Fornitore'),
                        Toggle::make('is_cloud')
                            ->label('Software Cloud'),
                    ]),
                Section::make('Configurazione API')
                    ->schema([
                        TextInput::make('website_url')
                            ->label('Sito Web')
                            ->url()
                            ->prefix('https://'),
                        TextInput::make('api_url')
                            ->label('URL API')
                            ->url()
                            ->prefix('https://'),
                        TextInput::make('sandbox_url')
                            ->label('URL Sandbox')
                            ->url()
                            ->prefix('https://'),
                        TextInput::make('api_key_url')
                            ->label('URL API Key')
                            ->url()
                            ->prefix('https://'),
                        Textarea::make('api_parameters')
                            ->label('Parametri API')
                            ->rows(3)
                            ->helperText('Inserisci i parametri API in formato JSON se necessario'),
                    ]),
            ]);
    }
}
