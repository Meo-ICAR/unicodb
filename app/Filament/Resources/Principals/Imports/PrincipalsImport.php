<?php

namespace App\Filament\Resources\Principals\Imports;

use App\Models\Principal;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PrincipalsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Principal
    {
        return new Principal([
            'name' => $row['name'] ?? null,
            'vat_number' => $row['partita_iva'] ?? null,
            'oam_number' => $row['numero_oam'] ?? null,
            'oam_at' => $this->parseDate($row['data_iscrizione_oam'] ?? null),
            'oam_name' => $row['nome_registro_oam'] ?? null,
            'company_id' => $row['company_id'] ?? null,
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'partita_iva' => 'nullable|string|max:50',
            'numero_oam' => 'nullable|string|max:50',
            'data_iscrizione_oam' => 'nullable|date',
            'nome_registro_oam' => 'nullable|string|max:255',
            'company_id' => 'nullable|string|max:36',
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
}
