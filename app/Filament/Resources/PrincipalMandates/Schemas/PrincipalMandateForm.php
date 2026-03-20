<?php

namespace App\Filament\Resources\PrincipalMandates\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PrincipalMandateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('mandate_number')
                    ->required()
                    ->maxLength(100),
                TextInput::make('name')
                    ->maxLength(255),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date'),
                Toggle::make('is_exclusive')
                    ->required(),
                Select::make('status')
                    ->options([
                        'ATTIVO' => 'ATTIVO',
                        'SCADUTO' => 'SCADUTO',
                        'RECEDUTO' => 'RECEDUTO',
                        'SOPESO' => 'SOPESO',
                    ])
                    ->default('ATTIVO'),
                FileUpload::make('contract_file_path')
                    ->directory('mandates'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
