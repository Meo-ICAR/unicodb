<?php

namespace App\Filament\Resources\AuditItems\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AuditItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('audit_id')
                    ->required()
                    ->numeric(),
                TextInput::make('auditable_type')
                    ->required(),
                TextInput::make('auditable_id')
                    ->required(),
                TextInput::make('name'),
                Select::make('result')
                    ->options([
            'OK' => 'O k',
            'RILIEVO' => 'R i l i e v o',
            'GRAVE_INADEMPIENZA' => 'G r a v e  i n a d e m p i e n z a',
            'NON_CONTROLLATO' => 'N o n  c o n t r o l l a t o',
        ])
                    ->default('OK'),
                Textarea::make('finding_description')
                    ->columnSpanFull(),
                Textarea::make('remediation_plan')
                    ->columnSpanFull(),
                DatePicker::make('remediation_deadline'),
                Toggle::make('is_resolved'),
            ]);
    }
}
