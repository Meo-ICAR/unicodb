<?php

namespace App\Filament\Imports;

use App\Models\Practice;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;

class PracticesImporter extends Importer
{
    protected static ?string $model = Practice::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('company_id')
                ->requiredMapping()
                ->rules(['required', 'max:36']),
            ImportColumn::make('principal_id')
                ->label('Mandante ID')
                ->rules(['nullable', 'integer']),
            ImportColumn::make('agent_id')
                ->label('Agente ID')
                ->rules(['nullable', 'integer']),
            ImportColumn::make('practice_number')
                ->label('Numero Pratica')
                ->requiredMapping()
                ->rules(['required', 'max:50']),
            ImportColumn::make('practice_date')
                ->label('Data Pratica')
                ->requiredMapping()
                ->rules(['required', 'date']),
            ImportColumn::make('amount')
                ->label('Importo')
                ->numeric()
                ->requiredMapping()
                ->rules(['required', 'numeric']),
            ImportColumn::make('duration_months')
                ->label('Durata Mesi')
                ->integer()
                ->rules(['nullable', 'integer']),
            ImportColumn::make('interest_rate')
                ->label('Tasso Interesse')
                ->numeric()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('purpose')
                ->label('Finalità')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('practice_status_id')
                ->label('Stato Pratica')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('practice_scope_id')
                ->label('Ambito Pratica')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('notes')
                ->label('Note')
                ->rules(['nullable']),
        ];
    }

    public function resolveRecord(): ?Practice
    {
        return new Practice();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $count = $import->successful_rows;
        return "Importate con successo {$count} " . str('pratica')->plural($count);
    }
}
