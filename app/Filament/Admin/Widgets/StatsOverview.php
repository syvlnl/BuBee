<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        $income = Transaction::incomes()->get()->sum('amount');
        $expense = Transaction::expenses()->get()->sum('amount');
        $cashFlow = $income - $expense;
        return [
            Stat::make('Income', $income)
                ->color('success'),
            Stat::make('Expense', $expense)
                ->color('danger'),
            Stat::make('Cash Flow', $cashFlow)
                ->color($cashFlow >= 0 ? 'success' : 'danger'),
        ];
    }
}
