<?php

namespace App\Filament\Resources\Employees\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Employee
    {
        return new Employee([
            'company_id' => $row['company_id'] ?? null,
            'name' => $row['nome'] ?? null,
            'role_title' => $row['ruolo'] ?? null,
            'cf' => $row['codice_fiscale'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['telefono'] ?? null,
            'department' => $row['dipartimento'] ?? null,
            'oam' => $row['oam'] ?? null,
            'ivass' => $row['ivass'] ?? null,
            'hiring_date' => $this->parseDate($row['data_assunzione'] ?? null),
            'termination_date' => $this->parseDate($row['data_cessazione'] ?? null),
            'is_active' => $this->parseBoolean($row['attivo'] ?? true),
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|string|max:36',
            'nome' => 'required|string|max:100',
            'ruolo' => 'nullable|string|max:100',
            'codice_fiscale' => 'nullable|string|max:16',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'dipartimento' => 'nullable|string|max:100',
            'oam' => 'nullable|string|max:100',
            'ivass' => 'nullable|string|max:100',
            'data_assunzione' => 'nullable|date',
            'data_cessazione' => 'nullable|date',
            'attivo' => 'required|boolean',
        ];
    }

    private function parseDate($date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            $dateObj = \DateTime::createFromFormat('d/m/Y', $date);
            return $dateObj ? $dateObj->format('Y-m-d') : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseBoolean($value): bool
    {
        if (empty($value)) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
