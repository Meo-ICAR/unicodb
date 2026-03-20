<?php

namespace App\Filament\Resources\PracticeCommissionStatuses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PracticeCommissionStatusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('status_payment')
                    ->label('Stato Pagamento')
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('Codice')
                    ->maxLength(20),
                Toggle::make('is_perfectioned')
                    ->label('Perfezionata')
                    ->default(false),
                Toggle::make('is_working')
                    ->label('In Lavorazione')
                    ->default(true),
            ]);
    }
}
