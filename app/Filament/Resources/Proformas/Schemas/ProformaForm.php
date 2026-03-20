<?php

namespace App\Filament\Resources\Proformas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProformaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('agent_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name'),
                TextInput::make('commission_label'),
                TextInput::make('total_commissions')
                    ->numeric(),
                TextInput::make('enasarco_retained')
                    ->numeric(),
                TextInput::make('remburse')
                    ->numeric(),
                TextInput::make('remburse_label'),
                TextInput::make('contribute')
                    ->numeric(),
                TextInput::make('contribute_label'),
                TextInput::make('refuse')
                    ->numeric(),
                TextInput::make('refuse_label'),
                TextInput::make('net_amount')
                    ->numeric(),
                TextInput::make('month')
                    ->numeric(),
                TextInput::make('year')
                    ->numeric(),
                Select::make('status')
                    ->options([
            'INSERITO' => 'I n s e r i t o',
            'INVIATO' => 'I n v i a t o',
            'ANNULLATO' => 'A n n u l l a t o',
            'FATTURATO' => 'F a t t u r a t o',
            'PAGATO' => 'P a g a t o',
            'STORICO' => 'S t o r i c o',
        ])
                    ->default('INSERITO')
                    ->required(),
            ]);
    }
}
