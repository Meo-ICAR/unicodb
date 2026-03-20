<?php

namespace App\Filament\Imports;

use App\Models\Proforma;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;

class ProformasImporter extends Importer
{
    protected static ?string $model = Proforma::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('company_id')
                ->requiredMapping()
                ->rules(['required', 'max:36']),
            ImportColumn::make('practice_id')
                ->label('Pratica ID')
                ->rules(['nullable', 'integer']),
            ImportColumn::make('proforma_number')
                ->label('Numero Proforma')
                ->requiredMapping()
                ->rules(['required', 'max:50']),
            ImportColumn::make('proforma_date')
                ->label('Data Proforma')
                ->requiredMapping()
                ->rules(['required', 'date']),
            ImportColumn::make('amount')
                ->label('Importo')
                ->numeric()
                ->requiredMapping()
                ->rules(['required', 'numeric']),
            ImportColumn::make('commission_amount')
                ->label('Commissione')
                ->numeric()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('net_amount')
                ->label('Netto')
                ->numeric()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('proforma_status_id')
                ->label('Stato Proforma')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('due_date')
                ->label('Data Scadenza')
                ->rules(['nullable', 'date']),
            ImportColumn::make('payment_date')
                ->label('Data Pagamento')
                ->rules(['nullable', 'date']),
            ImportColumn::make('invoice_number')
                ->label('Numero Fattura')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('notes')
                ->label('Note')
                ->rules(['nullable']),
        ];
    }

    public function resolveRecord(): ?Proforma
    {
        return new Proforma();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $count = $import->successful_rows;
        return "Importate con successo {$count} " . str('proforma')->plural($count);
    }
}
