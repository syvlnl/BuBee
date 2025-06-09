<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class WidgetExpenseChart extends ChartWidget
{
    protected static ?string $heading = 'Expenses';

    protected function getData(): array
    {
        $expense = Trend::query(Transaction::expenses())
            ->between(
                now()->startOfYear(),
                now()->endOfYear(),
            )
            ->perMonth()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'expense per month',
                    'data' => $expense->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#dc3545',
                    'backgroundColor' => 'rgba(220, 53, 69, 0.2)',
                    'pointBackgroundColor' => '#dc3545',
                ],
            ],
            'labels' => $expense->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
