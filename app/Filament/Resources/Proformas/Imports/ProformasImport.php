<?php

namespace App\Filament\Resources\Proformas\Imports;

use App\Models\Proforma;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProformasImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Proforma
    {
        return new Proforma([
            'company_id' => $row['company_id'] ?? null,
            'practice_id' => $row['pratica_id'] ?? null,
            'proforma_number' => $row['numero_proforma'] ?? null,
            'proforma_date' => $this->parseDate($row['data_proforma'] ?? null),
            'amount' => $row['importo'] ?? null,
            'commission_amount' => $row['commissione'] ?? null,
            'net_amount' => $row['netto'] ?? null,
            'proforma_status_id' => $this->getProformaStatusId($row['stato_proforma'] ?? null),
            'due_date' => $this->parseDate($row['data_scadenza'] ?? null),
            'payment_date' => $this->parseDate($row['data_pagamento'] ?? null),
            'invoice_number' => $row['numero_fattura'] ?? null,
            'notes' => $row['note'] ?? null,
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
            'pratica_id' => 'nullable|integer',
            'numero_proforma' => 'required|string|max:50',
            'data_proforma' => 'required|date',
            'importo' => 'required|numeric',
            'commissione' => 'nullable|numeric',
            'netto' => 'nullable|numeric',
            'stato_proforma' => 'nullable|string|max:50',
            'data_scadenza' => 'nullable|date',
            'data_pagamento' => 'nullable|date',
            'numero_fattura' => 'nullable|string|max:50',
            'note' => 'nullable|string',
        ];
    }

    private function getProformaStatusId($status): ?int
    {
        if (empty($status)) {
            return null;
        }

        $statusMap = [
            'INSERITO' => 1,
            'INVIATO' => 2,
            'ANNULLATO' => 3,
            'FATTURATO' => 4,
            'PAGATO' => 5,
            'STORICO' => 6,
        ];

        return $statusMap[$status] ?? null;
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
