<?php

namespace App\Filament\Resources\ChecklistItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ChecklistItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('checklist_id')
                    ->relationship('checklist', 'name')
                    ->required(),
                TextInput::make('ordine')
                    ->label('Ordine')
                    ->nullable(),
                TextInput::make('phase')
                    ->label('Fase della checklist')
                    ->nullable()
                    ->helperText('Fase a cui appartiene questo elemento'),
                Toggle::make('is_phaseclose')
                    ->label('Attività di chiusura fase')
                    ->default(false)
                    ->helperText("Se è l'attività finale della fase"),
                TextInput::make('name')
                    ->label('Nome elemento')
                    ->nullable(),
                TextInput::make('item_code'),
                Textarea::make('question')
                    ->columnSpanFull(),
                Textarea::make('answer')
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('descriptioncheck')
                    ->label('Descrizione Verifica Conformità')
                    ->helperText('Descrizione verifica conformità da effettuare')
                    ->columnSpanFull(),
                Textarea::make('annotation')
                    ->columnSpanFull(),
                Toggle::make('is_required')
                    ->required(),
                Select::make('attach_model')
                    ->options(['principal' => 'Principal', 'agent' => 'Agent', 'company' => 'Company', 'audit' => 'Audit']),
                TextInput::make('attach_model_id'),
                TextInput::make('n_documents')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('repeatable_code'),
                TextInput::make('depends_on_code'),
                TextInput::make('depends_on_value'),
                Select::make('dependency_type')
                    ->options(['show_if' => 'Show if', 'hide_if' => 'Hide if']),
                TextInput::make('url_step'),
                TextInput::make('url_callback'),
            ]);
    }
}
