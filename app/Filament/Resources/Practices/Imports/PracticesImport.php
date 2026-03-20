<?php

namespace App\Filament\Resources\Practices\Imports;

use App\Models\Practice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PracticesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Practice
    {
        return new Practice([
            'company_id' => $row['company_id'] ?? null,
            'principal_id' => $row['mandante_id'] ?? null,
            'agent_id' => $row['agente_id'] ?? null,
            'practice_number' => $row['numero_pratica'] ?? null,
            'practice_date' => $this->parseDate($row['data_pratica'] ?? null),
            'amount' => $row['importo'] ?? null,
            'duration_months' => $row['durata_mesi'] ?? null,
            'interest_rate' => $row['tasso_interesse'] ?? null,
            'purpose' => $row['finalita'] ?? null,
            'practice_status_id' => $this->getPracticeStatusId($row['stato_pratica'] ?? null),
            'practice_scope_id' => $this->getPracticeScopeId($row['ambito_pratica'] ?? null),
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
            'mandante_id' => 'nullable|integer',
            'agente_id' => 'nullable|integer',
            'numero_pratica' => 'required|string|max:50',
            'data_pratica' => 'required|date',
            'importo' => 'required|numeric',
            'durata_mesi' => 'nullable|integer',
            'tasso_interesse' => 'nullable|numeric',
            'finalita' => 'nullable|string|max:255',
            'stato_pratica' => 'nullable|string|max:50',
            'ambito_pratica' => 'nullable|string|max:100',
            'note' => 'nullable|string',
        ];
    }

    private function getPracticeStatusId($status): ?int
    {
        if (empty($status)) {
            return null;
        }

        $statusMap = [
            'Istruttoria' => 1,
            'Deliberata' => 2,
            'Erogata' => 3,
            'Respinta' => 4,
        ];

        return $statusMap[$status] ?? null;
    }

    private function getPracticeScopeId($scope): ?int
    {
        if (empty($scope)) {
            return null;
        }

        $scopeMap = [
            'Mutuo Ipotecario' => 1,
            'Cessione del Quinto' => 2,
            'Prestito Personale' => 3,
        ];

        return $scopeMap[$scope] ?? null;
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
