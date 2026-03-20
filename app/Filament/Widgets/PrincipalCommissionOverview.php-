<?php

namespace App\Filament\Widgets;

use App\Models\PrincipalCommissionGroup;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget;

class PrincipalCommissionOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalGroups = PrincipalCommissionGroup::matched()->count();
        $totalCommission = PrincipalCommissionGroup::matched()->sum('total_commission_amount');
        $totalInvoice = PrincipalCommissionGroup::matched()->sum('total_invoice_amount');
        $avgPercentage = PrincipalCommissionGroup::matched()->avg('commission_percentage');

        $highPercentage = PrincipalCommissionGroup::matched()->where('commission_percentage', '>', 200)->count();
        $lowPercentage = PrincipalCommissionGroup::matched()->where('commission_percentage', '<', 50)->count();

        return [
            Stat::make('Totali Match', $totalGroups)
                ->description('Gruppi con match')
                ->icon('heroicon-o-banknotes')
                ->color('primary'),
            Stat::make('Commissioni Totali', '€' . number_format($totalCommission, 2))
                ->description('Somma commissioni')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make('Importo Fatture', '€' . number_format($totalInvoice, 2))
                ->description('Somma fatture')
                ->icon('heroicon-o-document-text')
                ->color('info'),
            Stat::make('Percentuale Media', number_format($avgPercentage, 1) . '%')
                ->description('Media commissioni')
                ->icon('heroicon-o-chart-bar')
                ->color(function () {
                    if ($avgPercentage > 150)
                        return 'danger';
                    if ($avgPercentage > 100)
                        return 'warning';
                    return 'success';
                }),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
