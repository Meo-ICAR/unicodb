<?php

namespace App\Filament\Imports;

use App\Models\Client;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;

class ClientsImporter extends Importer
{
    protected static ?string $model = Client::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('company_id')
                ->requiredMapping()
                ->rules(['required', 'max:36']),
            ImportColumn::make('client_type_id')
                ->label('Tipo Cliente')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('first_name')
                ->label('Nome')
                ->requiredMapping()
                ->rules(['required', 'max:100']),
            ImportColumn::make('last_name')
                ->label('Cognome')
                ->requiredMapping()
                ->rules(['required', 'max:100']),
            ImportColumn::make('tax_code')
                ->label('Codice Fiscale')
                ->rules(['nullable', 'max:16']),
            ImportColumn::make('vat_number')
                ->label('Partita IVA')
                ->rules(['nullable', 'max:11']),
            ImportColumn::make('email')
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('phone')
                ->label('Telefono')
                ->rules(['nullable', 'max:20']),
            ImportColumn::make('birth_date')
                ->label('Data Nascita')
                ->rules(['nullable', 'date']),
            ImportColumn::make('birth_city')
                ->label('Comune Nascita')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('address')
                ->label('Indirizzo')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('city')
                ->label('Città')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('province')
                ->label('Provincia')
                ->rules(['nullable', 'max:2']),
            ImportColumn::make('postal_code')
                ->label('CAP')
                ->rules(['nullable', 'max:5']),
            ImportColumn::make('country')
                ->label('Stato')
                ->rules(['nullable', 'max:2']),
            ImportColumn::make('income')
                ->label('Reddito')
                ->numeric()
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('is_active')
                ->label('Attivo')
                ->boolean()
                ->requiredMapping()
                ->rules(['required', 'boolean']),
        ];
    }

    public function resolveRecord(): ?Client
    {
        return new Client();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $count = $import->successful_rows;
        return "Importati con successo {$count} " . str('cliente')->plural($count);
    }
}
