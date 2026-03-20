<?php

namespace App\Filament\Resources\Abis\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AbiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('abi')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Select::make('type')
                    ->options([
            'BANCA' => 'B a n c a',
            'INTERMEDIARIO_106' => 'I n t e r m e d i a r i o 106',
            'IP_IMEL' => 'I p  i m e l',
        ])
                    ->required(),
                TextInput::make('capogruppo'),
                TextInput::make('status')
                    ->required()
                    ->default('OPERATIVO'),
                DatePicker::make('data_iscrizione'),
                DatePicker::make('data_cancellazione'),
            ]);
    }
}
