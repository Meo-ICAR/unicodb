<?php
namespace App\Exports;

use App\Models\PracticeOam;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PracticeOamBaseExport implements FromQuery, WithHeadings
{
    public function query()
    {
        return PracticeOam::query()
            ->reorder()  // <--- FONDAMENTALE: rimuove l'order by id automatico
            ->select([
                'id',
                'oam_name',
                DB::raw('SUM(is_conventioned)'),
                DB::raw('SUM(is_notconventioned)'),
                DB::raw('SUM(is_perfected)'),
                DB::raw('SUM(is_working)'),
                DB::raw('SUM(erogato)'),
                DB::raw('SUM(erogato_lavorazione)'),
                DB::raw('SUM(compenso_cliente)'),
                DB::raw('SUM(compenso)'),
                DB::raw('SUM(compenso_lavorazione)'),
                DB::raw('SUM(provvigione)'),
            ])
            ->groupBy('oam_name');
    }

    public function headings(): array
    {
        return [
            'B-OAM', 'C-Convenzionata', 'D-Non_Convenzionata', 'E-Intermediate',
            'F-Lavorazione', 'G-Erogato', 'H-Erogato_Lavorazione',
            'I-Provv_Cliente', 'J-Provv_Istituto', 'K-Provv_Istituto_Lavorazione', 'L-Provv_Rete'
        ];
    }
}
