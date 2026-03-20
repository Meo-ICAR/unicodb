<?php

namespace App\Filament\Resources\SosReports\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SosReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('auiRecord.id')
                    ->label('Aui record')
                    ->placeholder('-'),
                TextEntry::make('client_mandate_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('company.name')
                    ->label('Company'),
                TextEntry::make('codice_protocollo_interno'),
                TextEntry::make('stato')
                    ->badge(),
                TextEntry::make('grado_sospetto')
                    ->badge(),
                TextEntry::make('motivo_sospetto')
                    ->columnSpanFull(),
                TextEntry::make('decisione_finali')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('data_segnalazione_uif')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('protocollo_uif')
                    ->placeholder('-'),
                TextEntry::make('responsabile.name')
                    ->label('Responsabile')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
