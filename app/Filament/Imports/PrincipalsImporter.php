<?php

namespace App\Filament\Imports;

use App\Models\Principal;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;

class PrincipalsImporter extends Importer
{
    protected static ?string $model = Principal::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('vat_number')
                ->label('Partita IVA')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('oam_number')
                ->label('Numero OAM')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('oam_at')
                ->label('Data Iscrizione OAM')
                ->rules(['nullable', 'date']),
            ImportColumn::make('oam_name')
                ->label('Nome Registro OAM')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('company_id')
                ->rules(['nullable', 'max:36']),
        ];
    }

    public function resolveRecord(): ?Principal
    {
        return new Principal();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $count = $import->successful_rows;
        return "Importati con successo {$count} " . str('mandante')->plural($count);
    }
}
