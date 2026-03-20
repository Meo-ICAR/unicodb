<?php

namespace App\Filament\Resources\AuiRecords\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AuiRecordInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('activityLog.id')
                    ->label('Log Attività')
                    ->placeholder('-'),
                TextEntry::make('practice.name')
                    ->label('Pratica')
                    ->placeholder('-'),
                TextEntry::make('client.name')
                    ->label('Cliente')
                    ->placeholder('-'),
                TextEntry::make('codice_univoco_aui'),
                TextEntry::make('tipo_registrazione'),
                TextEntry::make('data_registrazione')
                    ->date(),
                TextEntry::make('importo_operazione')
                    ->numeric(),
                TextEntry::make('profilo_rischio'),
                IconEntry::make('is_annullato')
                    ->boolean(),
                TextEntry::make('motivo_annullamento')
                    ->placeholder('-'),
                TextEntry::make('company.name')
                    ->label('Azienda')
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
