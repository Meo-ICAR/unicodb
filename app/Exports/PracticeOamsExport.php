<?php

namespace App\Exports;

use App\Models\PracticeOam;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Facades\Excel;

// use Maatwebsite\Excel\Excel;

class PracticeOamsExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return $this->collection;
    }

    public function export(Excel $excelEngine)
    {
        $records = PracticeOam::with(['practice.scopeOAM', 'practice.clients'])->get();
        $filename = 'OAM_Vigilanza_' . now()->format('Y-m-d') . '.xlsx';

        return $excelEngine->download(new PracticeOamExport($records), $filename);
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->collection() as $record) {
            $clientNames = '';
            if ($record->practice && $record->practice->clients) {
                $clientNames = $record->practice->clients->pluck('name')->join(', ');
            }

            $data[] = [
                'Codice OAM' => $record->practice->scopeOAM->oam_code ?? '',
                'Tipo OAM' => $record->practice->scopeOAM->tipo_prodotto ?? '',
                'Prodotto' => $record->tipo_prodotto,
                'Convenzionata' => $record->is_conventioned ? 'Sì' : 'No',
                'Codice Pratica' => $record->practice->CRM_code ?? '',
                'Nome Pratica' => $record->practice->name ?? '',
                'Cliente' => $clientNames,
                'Provvigione Assicurazione' => number_format($record->provvigione_assicurazione, 2, ',', '.') . ' €',
                'Provvigione Storno' => number_format($record->provvigione_storno, 2, ',', '.') . ' €',
                'Importo Lordo' => number_format($record->importo_lordo, 2, ',', '.') . ' €',
                'Netto Incassato' => number_format($record->netto_incassato, 2, ',', '.') . ' €',
                'Erogato' => number_format($record->erogato, 2, ',', '.') . ' €',
                'Data Perfezionamento' => $record->data_perfezionamento ? $record->data_perfezionamento->format('d/m/Y') : '',
                'Mandante' => $record->name,
                'Mese' => $record->mese,
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        if (empty($this->collection())) {
            return [];
        }

        $firstRecord = $this->collection()->first();
        $headings = [
            'Codice OAM',
            'Tipo OAM',
            'Prodotto',
            'Convenzionata',
            'Codice Pratica',
            'Nome Pratica',
            'Cliente',
            'Provvigione Assicurazione',
            'Provvigione Storno',
            'Importo Lordo',
            'Netto Incassato',
            'Erogato',
            'Data Perfezionamento',
            'Mandante',
            'Mese'
        ];

        // Add client names if available
        if ($firstRecord && $firstRecord->practice && $firstRecord->practice->clients) {
            $clientNames = $firstRecord->practice->clients->pluck('name')->join(', ');
            $headings[] = 'Clienti';
        }

        return $headings;
    }

    public function title(): string
    {
        return 'Vigilanza OAM';
    }
}
