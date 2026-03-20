<?php

namespace App\Filament\Resources\DocumentStatuses\Schemas;

use App\Models\DocumentStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DocumentStatusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informazioni Generali')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome Stato')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Es: OK, Da Verificare, Difforme')
                            ->helperText('Nome descrittivo dello stato del documento'),
                        Select::make('status')
                            ->label('Codice Stato')
                            ->required()
                            ->options(DocumentStatus::STATUSES)
                            ->searchable()
                            ->placeholder('Seleziona uno stato')
                            ->helperText('Codice univoco per lo stato'),
                        TextInput::make('description')
                            ->label('Descrizione')
                            ->maxLength(500)
                            ->placeholder('Descrizione dettagliata dello stato')
                            ->helperText('Spiegazione di cosa significa questo stato'),
                    ])
                    ->columns(2),
                Section::make('Impostazioni Stato')
                    ->schema([
                        Toggle::make('is_ok')
                            ->label('Stato Positivo')
                            ->helperText('Indica se questo è uno stato positivo (documento valido)'),
                        Toggle::make('is_rejected')
                            ->label('Stato Rifiutato')
                            ->helperText('Indica se questo è uno stato di rifiuto (documento non valido)'),
                    ])
                    ->columns(2),
            ]);
    }
}
