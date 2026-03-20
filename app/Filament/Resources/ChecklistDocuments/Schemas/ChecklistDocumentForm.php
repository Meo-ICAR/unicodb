<?php

namespace App\Filament\Resources\ChecklistDocuments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ChecklistDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('practice_scope_id')
                    ->label('Tipo Pratica')
                    ->relationship('practiceScope')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('document_type_id')
                    ->label('Tipo Documento')
                    ->relationship('documentType')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('principal_id')
                    ->label('Banca/Ente')
                    ->relationship('principal')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Opzionale: se non specificato, vale per tutte le banche'),
                Toggle::make('is_required')
                    ->label('Obbligatorio')
                    ->default(true),
                Textarea::make('description')
                    ->label('Note/Condizioni')
                    ->rows(3)
                    ->helperText('Es: "Solo se coniugato", "Richiesto per importi > 50.000€"'),
            ]);
    }
}
