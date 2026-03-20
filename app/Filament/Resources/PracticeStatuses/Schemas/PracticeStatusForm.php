<?php

namespace App\Filament\Resources\PracticeStatuses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PracticeStatusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('practice_id')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required(),
                TextInput::make('name'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextInput::make('changed_by')
                    ->required()
                    ->numeric(),
            ]);
    }
}
