<?php

namespace App\Filament\Resources\TrainingTemplates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TrainingTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('category')
                    ->options([
            'OAM' => 'O a m',
            'IVASS' => 'I v a s s',
            'GDPR' => 'G d p r',
            'SICUREZZA' => 'S i c u r e z z a',
            'PRODOTTO' => 'P r o d o t t o',
            'SOFT_SKILLS' => 'S o f t  s k i l l s',
        ])
                    ->required(),
                TextInput::make('base_hours')
                    ->required()
                    ->numeric(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Toggle::make('is_mandatory'),
            ]);
    }
}
