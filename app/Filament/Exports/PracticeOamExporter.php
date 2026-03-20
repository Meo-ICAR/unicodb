<?php

namespace App\Filament\Exports;

use App\Models\PracticeOam;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Illuminate\Database\Eloquent\Builder;

class PracticeOamExporter extends Exporter
{
    protected static ?string $model = PracticeOam::class;

    public static function modifyQuery(Builder $query): Builder
    {
        // Caricamento ottimizzato per evitare centinaia di query al database
        return $query->with([
            'practice.clients',
            'practice.principal'
        ]);
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('oam_code')
                ->label('B-OAM Code'),
            ExportColumn::make('tipo_prodotto')
                ->label('Prodotto'),
            // Trasformiamo i booleani in 1/0 per riflettere i tuoi sommari numerici
            ExportColumn::make('is_conventioned')
                ->label('C - Convenzionata')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('is_notconventioned')
                ->label('D - NON Convenz.')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('is_perfected')
                ->label('E - Intermediate')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('is_working')
                ->label('F - Lavorazione')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('erogato')
                ->label('G - Erogato'),
            ExportColumn::make('erogato_lavorazione')
                ->label('H - Lavorazione'),
            ExportColumn::make('compenso_cliente')
                ->label('I - Provv. Cliente'),
            ExportColumn::make('compenso')
                ->label('J - Provv. Istituto'),
            ExportColumn::make('compenso_premio')
                ->label('K - Premio'),
            ExportColumn::make('compenso_assicurazione')
                ->label('L - Assicurativi'),
            ExportColumn::make('provvigione')
                ->label('O - Provv. Rete'),
            ExportColumn::make('provvigione_assicurazione')
                ->label('P - Assic. Rete'),
            ExportColumn::make('is_cancel')
                ->label('S - N.Rivalse')
                ->formatStateUsing(fn($state) => $state ? 1 : 0),
            ExportColumn::make('storno')
                ->label('T - Rivalsa'),
            ExportColumn::make('compenso_rimborso'),
            ExportColumn::make('provvigione_premio'),
            ExportColumn::make('provvigione_storno'),
            ExportColumn::make('provvigione_rimborso'),
            ExportColumn::make('name')
                ->label('Mandante'),
            // Gestione dei nomi clienti (concatena se sono multipli)
            ExportColumn::make('practice.clients')
                ->label('Cliente')
                ->formatStateUsing(fn($state) => $state->pluck('name')->implode(', ')),
            ExportColumn::make('practice.CRM_code')
                ->label('Codice'),
            ExportColumn::make('practice.name')
                ->label('Pratica'),
            ExportColumn::make('practice.inserted_at')
                ->label('Inserita'),
            ExportColumn::make('practice.erogated_at')
                ->label('Erogata'),
            ExportColumn::make('practice.principal.type')
                ->label('Tipo fin.'),
            ExportColumn::make('compenso_lavorazione'),
            ExportColumn::make('provvigione_lavorazione'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = "L'esportazione di Practice OAM è stata completata e " . number_format($export->successful_rows) . ' righe sono state elaborate.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' righe non sono state esportate a causa di errori.';
        }

        return $body;
    }
}
