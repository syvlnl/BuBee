<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class WidgetIncomeChart extends ChartWidget
{
    protected static ?string $heading = 'Incomes';

    protected function getData(): array
    {
        $income = Trend::query(Transaction::incomes())
            ->between(
                now()->startOfYear(),
                now()->endOfYear(),
            )
            ->perMonth()
            ->sum('amount');

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
                    'label' => 'Income per month',
                    'data' => $income->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#28a745',
                    'backgroundColor' => 'rgba(40, 167, 69, 0.2)',
                    'pointBackgroundColor' => '#28a745',
                ],
                [
                    'label' => 'expense per month',
                    'data' => $expense->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#dc3545',
                    'backgroundColor' => 'rgba(220, 53, 69, 0.2)',
                    'pointBackgroundColor' => '#dc3545',
                ],
            ],
            'labels' => $income->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
