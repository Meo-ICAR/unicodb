<?php

namespace App\Filament\Resources\AuiRecords\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AuiRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('activity_log_id')
                    ->relationship('activityLog', 'id'),
                Select::make('practice_id')
                    ->relationship('practice', 'name'),
                Select::make('client_id')
                    ->relationship('client', 'name'),
                TextInput::make('codice_univoco_aui')
                    ->required(),
                TextInput::make('tipo_registrazione')
                    ->required(),
                DatePicker::make('data_registrazione')
                    ->required(),
                TextInput::make('importo_operazione')
                    ->required()
                    ->numeric(),
                TextInput::make('profilo_rischio')
                    ->required()
                    ->default('basso'),
                Toggle::make('is_annullato')
                    ->required(),
                TextInput::make('motivo_annullamento'),
            ]);
    }
}
