<?php

namespace App\Filament\Exports;

use App\Models\PracticeOam;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class PracticeOamAnaliticoExporter extends Exporter
{
    protected static ?string $model = PracticeOam::class;

    public static function modifyQuery(Builder $query): Builder
    {
        // Caricamento ottimizzato per evitare centinaia di query al database
        return $query
            ->reorder()  // <--- Rimuove qualsiasi orderBy automatico (tipo l'ID)
            ->select([
                'oam_name as id',
                'oam_name as B_OAM',  // Colonna interna alla tabella
                DB::raw('SUM(is_conventioned) as C_Convenzionata'),
                DB::raw('SUM(is_notconventioned) as D_Non_Convenzionata'),
                DB::raw('SUM(is_perfected) as E_Intermediate'),
                DB::raw('SUM(is_working) as F_Lavorazione'),
                DB::raw('SUM(erogato) as G_Erogato'),
                DB::raw('SUM(erogato_lavorazione) as H_Erogato_Lavorazione'),
                DB::raw('SUM(compenso_cliente) as I_Provvigione_Cliente'),
                DB::raw('SUM(compenso) as J_Provvigione_Istituto'),
                DB::raw('SUM(compenso_lavorazione) as K_Provvigione_Istituto_Lavorazione'),
                DB::raw('SUM(provvigione) as L_Provvigione_Rete'),
                // Aggiungi qui tutte le altre somme che ti servono (es. storno, premio, etc)
            ])
            ->groupBy('oam_name');
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('oam_name')
                ->label('B-OAM'),
            ExportColumn::make('is_conventioned')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('is_notconventioned')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('is_perfected')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('is_working')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('erogato'),
            ExportColumn::make('erogato_lavorazione'),
            ExportColumn::make('compenso_cliente'),
            ExportColumn::make('compenso'),
            ExportColumn::make('compenso_premio'),
            ExportColumn::make('compenso_assicurazione'),
            ExportColumn::make('provvigione'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your practice oam base export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
