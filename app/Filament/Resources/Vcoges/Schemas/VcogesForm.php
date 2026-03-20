<?php

namespace App\Filament\Resources\Vcoges\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VcogesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('mese')
                    ->label('Mese')
                    ->required()
                    ->format('Y-m')
                    ->displayFormat('F Y')
                    ->helperText('Seleziona il mese di riferimento'),
                TextInput::make('entrata')
                    ->label('Entrata')
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01)
                    ->helperText('Totale entrate del mese'),
                TextInput::make('uscita')
                    ->label('Uscita')
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01)
                    ->helperText('Totale uscite del mese'),
            ]);
    }
}
