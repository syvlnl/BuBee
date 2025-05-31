<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;

class WidgetExpenseChart extends ChartWidget
{
    protected static ?string $heading = 'Expenses';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => [5, 15, 25, 30, 45, 60, 80, 90, 100, 110, 125, 140],
                    'borderColor' => '#28a745', 
                    'backgroundColor' => 'rgba(40, 167, 69, 0.2)',
                    'pointBackgroundColor' => '#28a745',
                ],
                [
                    'label' => 'Expense',
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                    'borderColor' => '#dc3545', 
                    'backgroundColor' => 'rgba(220, 53, 69, 0.2)',
                    'pointBackgroundColor' => '#dc3545',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
