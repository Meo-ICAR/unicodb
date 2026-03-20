<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ComplianceOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalPractices = \App\Models\Practice::count();

        $missingPrivacy = \App\Models\Practice::whereDoesntHave('documents', function ($q) {
            $q->whereHas('documentType', function ($q) {
                $q->whereHas('scopes', fn($s) => $s->where('document_scopes.name', 'Privacy'));
            });
        })->count();

        $missingAML = \App\Models\Practice::whereDoesntHave('documents', function ($q) {
            $q->whereHas('documentType', function ($q) {
                $q->whereHas('scopes', fn($s) => $s->where('document_scopes.name', 'AML'));
            });
        })->count();

        $missingOAM = \App\Models\Practice::whereDoesntHave('documents', function ($q) {
            $q->whereHas('documentType', function ($q) {
                $q->whereHas('scopes', fn($s) => $s->where('document_scopes.name', 'OAM'));
            });
        })->count();

        return [
            Stat::make('Pratiche Totali', $totalPractices)
                ->description('Totale fascicoli in gestione')
                ->icon('heroicon-o-folder'),
            Stat::make('Privacy Mancanti', $missingPrivacy)
                ->description('Pratiche senza consenso GDPR')
                ->color($missingPrivacy > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-document-text'),
            Stat::make('AML Da Verificare', $missingAML)
                ->description('Documenti antiriciclaggio assenti')
                ->color($missingAML > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-shield-exclamation'),
            Stat::make('OAM Non Caricati', $missingOAM)
                ->description('Moduli OAM mancanti')
                ->color($missingOAM > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-exclamation-triangle'),
        ];
    }
}
