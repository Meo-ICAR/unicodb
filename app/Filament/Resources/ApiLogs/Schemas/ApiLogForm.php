<?php

namespace App\Filament\Resources\ApiLogs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ApiLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('api_configuration_id')
                    ->required()
                    ->numeric(),
                TextInput::make('api_loggable_type'),
                TextInput::make('api_loggable_id'),
                TextInput::make('endpoint')
                    ->required(),
                Select::make('method')
                    ->options([
            'GET' => 'G e t',
            'POST' => 'P o s t',
            'PUT' => 'P u t',
            'DELETE' => 'D e l e t e',
            'PATCH' => 'P a t c h',
        ])
                    ->required(),
                TextInput::make('name'),
                TextInput::make('request_payload'),
                TextInput::make('response_payload'),
                TextInput::make('status_code')
                    ->numeric(),
                TextInput::make('execution_time_ms')
                    ->numeric(),
                Textarea::make('error_message')
                    ->columnSpanFull(),
            ]);
    }
}
