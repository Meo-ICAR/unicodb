<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\Action;
use Filament\Actions\Imports\ImportAction;
use Filament\Actions\Imports\ResolveField;
use Filament\Actions\Imports\Heading;
use Filament\Actions\Imports\ModelImport;
use Filament\Imports\Import;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AgentsImporter implements ToCollection, WithHeadingRow, WithValidation
{
    public static function getModel(): string
    {
        return User::class;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            if (isset($row['name']) && isset($row['email'])) {
                User::updateOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'phone' => $row['phone'] ?? null,
                        'is_agent' => true,
                    ]
                );
            }
        }
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function getResolvedImports(): array
    {
        return [
            'name' => ResolveField::using('name'),
            'email' => ResolveField::using('email'),
        ];
    }
}
