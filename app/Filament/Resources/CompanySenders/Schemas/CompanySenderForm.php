<?php

namespace App\Filament\Resources\CompanySenders\Schemas;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanySenderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    Section::make('Informazioni Sender')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nome Sender')
                                ->required()
                                ->helperText('Nome identificativo del sender/inviatore'),
                            TextInput::make('email')
                                ->label('Email Sender')
                                ->email()
                                ->required()
                                ->helperText('Email principale del sender'),
                            Toggle::make('is_active')
                                ->label('Attivo')
                                ->default(true)
                                ->helperText('Abilita/disabilita questo sender'),
                            Textarea::make('description')
                                ->label('Descrizione')
                                ->rows(3)
                                ->nullable()
                                ->helperText('Descrizione dettagliata del sender'),
                        ]),
                    Section::make('Configurazione Eventi')
                        ->schema([
                            TextInput::make('eventgroup')
                                ->label('Gruppo Evento')
                                ->nullable()
                                ->helperText('Evento aziendale (es. pratiche, commissioni, audit)'),
                            TextInput::make('eventcode')
                                ->label('Codice Evento')
                                ->nullable()
                                ->helperText("Codice specifico dell'evento"),
                            Textarea::make('emails')
                                ->label('Email CC')
                                ->rows(3)
                                ->nullable()
                                ->helperText('Email a cui inviare per conoscenza (separate da virgola)'),
                        ]),
                ]),
            ]);
    }
}
