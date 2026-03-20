<?php

namespace App\Filament\Resources\PrincipalEmployees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PrincipalEmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('principal_id')
                    ->relationship('principal', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Banca/Principal'),
                TextInput::make('usercode')
                    ->required()
                    ->unique()
                    ->label('Codice Utente')
                    ->helperText('Codice identificativo univoco del dipendente'),
                TextInput::make('description')
                    ->label('Descrizione')
                    ->helperText('Ruolo o note sul dipendente')
                    ->nullable(),
                DatePicker::make('start_date')
                    ->required()
                    ->label('Data Inizio')
                    ->helperText('Data di inizio autorizzazione'),
                DatePicker::make('end_date')
                    ->label('Data Fine')
                    ->helperText('Data di fine autorizzazione (lasciare vuoto per indeterminato)')
                    ->nullable(),
                Toggle::make('is_active')
                    ->label('Attivo')
                    ->default(true)
                    ->helperText('Stato attuale del dipendente'),
            ]);
    }
}
