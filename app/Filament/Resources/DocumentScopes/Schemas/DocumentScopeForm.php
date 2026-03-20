<?php

namespace App\Filament\Resources\DocumentScopes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DocumentScopeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('description'),
                TextInput::make('color_code')
                    ->default('#6B7280'),
            ]);
    }
}
