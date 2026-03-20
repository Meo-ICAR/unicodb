<?php

namespace App\Filament\Imports;

use App\Models\Employee;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;

class EmployeesImporter extends Importer
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('company_id')
                ->requiredMapping()
                ->rules(['required', 'max:36']),
            ImportColumn::make('name')
                ->label('Nome')
                ->requiredMapping()
                ->rules(['required', 'max:100']),
            ImportColumn::make('role_title')
                ->label('Ruolo')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('cf')
                ->label('Codice Fiscale')
                ->rules(['nullable', 'max:16']),
            ImportColumn::make('email')
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('phone')
                ->label('Telefono')
                ->rules(['nullable', 'max:20']),
            ImportColumn::make('department')
                ->label('Dipartimento')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('oam')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('ivass')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('hiring_date')
                ->label('Data Assunzione')
                ->rules(['nullable', 'date']),
            ImportColumn::make('termination_date')
                ->label('Data Cessazione')
                ->rules(['nullable', 'date']),
            ImportColumn::make('is_active')
                ->label('Attivo')
                ->boolean()
                ->requiredMapping()
                ->rules(['required', 'boolean']),
        ];
    }

    public function resolveRecord(): ?Employee
    {
        return new Employee();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $count = $import->successful_rows;
        return "Importati con successo {$count} " . str('dipendente')->plural($count);
    }
}
