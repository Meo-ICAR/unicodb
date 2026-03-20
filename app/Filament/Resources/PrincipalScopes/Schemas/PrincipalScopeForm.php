<?php

namespace App\Filament\Resources\PrincipalScopes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PrincipalScopeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('practice_scope_id')
                    ->relationship('practiceScope', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
