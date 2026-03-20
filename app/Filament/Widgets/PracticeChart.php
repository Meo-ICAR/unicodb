<?php

namespace App\Filament\Widgets;

use App\Models\Practice;
use App\Models\PracticeScope;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PracticeChart extends ChartWidget
{
    //   protected static ?int $sort = 1;
    //   protected int|string|array $columnSpan = 'full';
    protected ?string $heading = 'Distribuzione Pratiche per Tipologia';
    //    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = Practice::selectRaw('practice_scope_id, COUNT(*) as count')
            ->groupBy('practice_scope_id')
            ->with('practiceScope')
            ->get();

        $labels = [];
        $values = [];
        $colors = [];

        $colorPalette = [
            '#3B82F6',  // blue
            '#10B981',  // green
            '#F59E0B',  // yellow
            '#EF4444',  // red
            '#8B5CF6',  // purple
            '#EC4899',  // pink
            '#14B8A6',  // teal
            '#F97316',  // orange
        ];

        foreach ($data as $index => $item) {
            if ($item->practiceScope) {
                $labels[] = $item->practiceScope->name;
                $values[] = $item->count;
                $colors[] = $colorPalette[$index % count($colorPalette)];
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const label = context.label || "";
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ": " + value + " pratiche (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
            'cutout' => '60%',
        ];
    }
}
