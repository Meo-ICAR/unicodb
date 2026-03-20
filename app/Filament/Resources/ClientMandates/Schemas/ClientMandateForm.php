<?php

namespace App\Filament\Resources\ClientMandates\Schemas;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ClientMandateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->relationship('client', 'name')
                    ->required(),
                TextInput::make('numero_mandato')
                    ->required()
                    ->default(function () {
                        // Genera automaticamente: MAND-CLIENT_ID-ANNO-PROGRESSIVO
                        $year = date('Y');

                        // Trova l'ultimo progressivo per questo anno
                        $lastProgressive = \App\Models\ClientMandate::whereYear('created_at', '=', $year)
                            ->orderBy('numero_mandato', 'desc')
                            ->first();

                        if ($lastProgressive) {
                            // Estrai il numero progressivo (es: MAND-000001-2026-001 -> 001)
                            preg_match('/MAND-\d{6}-\d{4}-(\d+)/', $lastProgressive->numero_mandato, $matches);
                            $progressive = ($matches[1] ?? '001') + 1;
                        } else {
                            $progressive = 1;
                        }

                        return 'MAND-' . str_pad($progressive, 6, '0', STR_PAD_LEFT) . "-{$year}";
                    }),
                DatePicker::make('data_firma_mandato')
                    ->required(),
                DatePicker::make('data_scadenza_mandato')
                    ->required(),
                TextInput::make('importo_richiesto_mandato')
                    ->numeric(),
                TextInput::make('scopo_finanziamento'),
                DatePicker::make('data_consegna_trasparenza'),
                Select::make('stato')
                    ->options([
                        'attivo' => 'Attivo',
                        'concluso_con_successo' => 'Concluso con successo',
                        'scaduto' => 'Scaduto',
                        'revocato' => 'Revocato',
                    ])
                    ->default('attivo')
                    ->required(),
            ]);
    }
}
