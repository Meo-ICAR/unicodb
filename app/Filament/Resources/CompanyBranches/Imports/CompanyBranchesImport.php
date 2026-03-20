<?php

namespace App\Filament\Resources\CompanyBranches\Imports;

use App\Models\CompanyBranch;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CompanyBranchesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?CompanyBranch
    {
        return new CompanyBranch([
            'company_id' => $row['company_id'] ?? null,
            'name' => $row['nome_sede'] ?? null,
            'is_main_office' => $this->parseBoolean($row['sede_principale'] ?? null),
            'manager_first_name' => $row['nome_responsabile'] ?? null,
            'manager_last_name' => $row['cognome_responsabile'] ?? null,
            'manager_email' => $row['email_responsabile'] ?? null,
            'manager_phone' => $row['telefono_responsabile'] ?? null,
            'address' => $row['indirizzo'] ?? null,
            'city' => $row['citta'] ?? null,
            'province' => $row['provincia'] ?? null,
            'postal_code' => $row['cap'] ?? null,
            'country' => $row['stato'] ?? null,
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
            'nome_sede' => 'required|string|max:255',
            'sede_principale' => 'required|boolean',
            'nome_responsabile' => 'nullable|string|max:100',
            'cognome_responsabile' => 'nullable|string|max:100',
            'email_responsabile' => 'nullable|email|max:255',
            'telefono_responsabile' => 'nullable|string|max:20',
            'indirizzo' => 'nullable|string|max:255',
            'citta' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:2',
            'cap' => 'nullable|string|max:5',
            'stato' => 'nullable|string|max:2',
        ];
    }

    private function parseBoolean($value): bool
    {
        if (empty($value)) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
