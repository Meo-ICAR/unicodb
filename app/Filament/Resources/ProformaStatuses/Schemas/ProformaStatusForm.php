<?php

namespace App\Filament\Resources\ProformaStatuses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProformaStatusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
                TextInput::make('color'),
                Toggle::make('is_deleted'),
                Toggle::make('is_payable'),
                Toggle::make('is_external'),
                Toggle::make('is_ok'),
            ]);
    }
}
