<?php

namespace App\Filament\Resources\Audits\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class AuditForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni Generali')
                    ->schema([
                        TextInput::make('title')
                            ->label('Titolo Audit')
                            ->required(),
                        Section::make('Richiedente Audit (Polimorfico)')
                            ->schema([
                                Select::make('requester_type')
                                    ->label('Tipo Richiedente')
                                    ->options([
                                        'App\Models\Principal' => 'Mandante',
                                        'App\Models\Agent' => 'Agente',
                                        'App\Models\RegulatoryBody' => 'Ente Regolatore',
                                        'App\Models\Company' => 'Azienda',
                                        'App\Models\Employee' => 'Dipendente',
                                    ])
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set) => $set('requester_id', null))
                                    ->required(),
                                Select::make('requester_id')
                                    ->label('Richiedente Selezionato')
                                    ->searchable()
                                    ->getSearchResultsUsing(function (string $search, callable $get) {
                                        $type = $get('requester_type');
                                        if (!$type)
                                            return [];

                                        $model = new $type;
                                        return $model::where('name', 'like', "%{$search}%")
                                            ->limit(50)
                                            ->pluck('name', 'id');
                                    })
                                    ->getOptionLabelUsing(function ($value, callable $get) {
                                        $type = $get('requester_type');
                                        if (!$type || !$value)
                                            return '';

                                        $model = new $type;
                                        $record = $model::find($value);
                                        return $record?->name ?? '';
                                    })
                                    ->required(),
                            ]),
                        TextInput::make('emails')
                            ->label('Email Notifiche')
                            ->email()
                            ->required(),
                        TextInput::make('reference_period')
                            ->label('Periodo di Riferimento'),
                    ]),
                Section::make('Oggetto Audit (Polimorfico)')
                    ->schema([
                        Select::make('auditable_type')
                            ->label('Tipo Oggetto')
                            ->options([
                                'App\Models\Company' => 'Azienda',
                                'App\Models\Agent' => 'Agente',
                                'App\Models\Employee' => 'Dipendente',
                                'App\Models\Client' => 'Cliente',
                                'App\Models\Principal' => 'Mandante',
                            ])
                            ->reactive()
                            ->afterStateUpdated(fn($state, callable $set) => $set('auditable_id', null))
                            ->required(),
                        Select::make('auditable_id')
                            ->label('Oggetto Selezionato')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search, callable $get) {
                                $type = $get('auditable_type');
                                if (!$type)
                                    return [];

                                $model = new $type;
                                return $model::where('name', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->pluck('name', 'id');
                            })
                            ->getOptionLabelUsing(function ($value, callable $get) {
                                $type = $get('auditable_type');
                                if (!$type || !$value)
                                    return '';

                                $model = new $type;
                                $record = $model::find($value);
                                return $record?->name ?? '';
                            })
                            ->required(),
                    ]),
                Section::make('Riferimenti Specifici (Legacy)')
                    ->schema([
                        Select::make('principal_id')
                            ->label('Mandante (Legacy)')
                            ->searchable()
                            ->nullable()
                            ->helperText('Usa il campo polimorfico sopra per nuove associazioni'),
                        Select::make('agent_id')
                            ->label('Agente (Legacy)')
                            ->searchable()
                            ->nullable()
                            ->helperText('Usa il campo polimorfico sopra per nuove associazioni'),
                        Select::make('regulatory_body_id')
                            ->label('Ente Regolatore')
                            ->searchable()
                            ->nullable(),
                        Select::make('client_id')
                            ->label('Cliente')
                            ->searchable()
                            ->nullable()
                            ->helperText('Usa il campo polimorfico sopra per nuove associazioni'),
                    ])
                    ->collapsed(),
                Section::make('Date e Stato')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Data Inizio')
                            ->required(),
                        DatePicker::make('end_date')
                            ->label('Data Fine'),
                        Select::make('status')
                            ->label('Stato')
                            ->options([
                                'PROGRAMMATO' => 'Programmato',
                                'IN_CORSO' => 'In Corso',
                                'COMPLETATO' => 'Completato',
                                'ARCHIVIATO' => 'Archiviato',
                            ])
                            ->default('PROGRAMMATO'),
                        TextInput::make('overall_score')
                            ->label('Valutazione Finale'),
                    ]),
            ]);
    }
}
