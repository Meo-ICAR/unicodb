<?php

namespace App\Filament\Imports;

use App\Models\PracticeCommission;
use Filament\Actions\Imports\Action;
use Filament\Actions\Imports\Heading;
use Filament\Actions\Imports\ImportAction;
use Filament\Actions\Imports\ModelImport;
use Filament\Actions\Imports\ResolveField;
use Filament\Imports\Import;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PracticeCommissionsImporter implements ToCollection, WithHeadingRow, WithValidation
{
    public static function getModel(): string
    {
        return PracticeCommission::class;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            if (isset($row['practice_name']) && isset($row['agent_name'])) {
                PracticeCommission::create([
                    'practice_id' => $this->getPracticeId($row['practice_name']),
                    'agent_id' => $this->getAgentId($row['agent_name']),
                    'commission_label' => $row['commission_label'] ?? null,
                    'total_commissions' => $row['total_commissions'] ?? 0,
                    'enasarco_retained' => $row['enasarco_retained'] ?? 0,
                    'remburse' => $row['remburse'] ?? 0,
                    'remburse_label' => $row['remburse_label'] ?? null,
                    'contribute' => $row['contribute'] ?? 0,
                    'contribute_label' => $row['contribute_label'] ?? null,
                    'refuse' => $row['refuse'] ?? 0,
                    'refuse_label' => $row['refuse_label'] ?? null,
                    'net_amount' => $row['net_amount'] ?? 0,
                    'month' => $row['month'] ?? null,
                    'year' => $row['year'] ?? null,
                    'status' => $row['status'] ?? 'pending',
                ]);
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
            'practice_name' => ['required', 'string'],
            'agent_name' => ['required', 'string'],
            'commission_label' => ['nullable', 'string'],
            'total_commissions' => ['nullable', 'numeric'],
            'enasarco_retained' => ['nullable', 'numeric'],
            'remburse' => ['nullable', 'numeric'],
            'remburse_label' => ['nullable', 'string'],
            'contribute' => ['nullable', 'numeric'],
            'contribute_label' => ['nullable', 'string'],
            'refuse' => ['nullable', 'numeric'],
            'refuse_label' => ['nullable', 'string'],
            'net_amount' => ['nullable', 'numeric'],
            'month' => ['nullable', 'integer'],
            'year' => ['nullable', 'integer'],
            'status' => ['nullable', 'string'],
        ];
    }

    public function getResolvedImports(): array
    {
        return [
            'practice_name' => ResolveField::using('practice.name'),
            'agent_name' => ResolveField::using('agent.name'),
        ];
    }

    private function getPracticeId(string $practiceName): ?int
    {
        $practice = \App\Models\Practice::where('name', $practiceName)->first();
        return $practice?->id;
    }

    private function getAgentId(string $agentName): ?int
    {
        $agent = \App\Models\User::where('name', $agentName)->first();
        return $agent?->id;
    }
}
