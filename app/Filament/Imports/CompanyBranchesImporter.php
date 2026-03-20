<?php

namespace App\Filament\Imports;

use App\Models\CompanyBranch;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;

class CompanyBranchesImporter extends Importer
{
    protected static ?string $model = CompanyBranch::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('company_id')
                ->requiredMapping()
                ->rules(['required', 'max:36']),
            ImportColumn::make('name')
                ->label('Nome Sede')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('is_main_office')
                ->label('Sede Principale')
                ->boolean()
                ->requiredMapping()
                ->rules(['required', 'boolean']),
            ImportColumn::make('manager_first_name')
                ->label('Nome Responsabile')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('manager_last_name')
                ->label('Cognome Responsabile')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('manager_email')
                ->label('Email Responsabile')
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('manager_phone')
                ->label('Telefono Responsabile')
                ->rules(['nullable', 'max:20']),
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
        ];
    }

    public function resolveRecord(): ?CompanyBranch
    {
        return new CompanyBranch();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $count = $import->successful_rows;
        return "Importate con successo {$count} " . str('sede')->plural($count);
    }
}
