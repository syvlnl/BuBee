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
        $startDate = !is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = !is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        $applyDateFilter = function ($query) use ($startDate, $endDate) {
            return $query
                ->when($startDate, fn($q) => $q->where('date_transaction', '>=', $startDate))
                ->when($endDate, fn($q) => $q->where('date_transaction', '<=', $endDate));
        };

        $income = $applyDateFilter(Transaction::incomes())->sum('amount');
        $expense = $applyDateFilter(Transaction::expenses())->sum('amount');
        $cashFlow = $income - $expense;

        $formatToRupiah = fn($amount) => 'Rp ' . number_format($amount, 0, ',', '.');

        return [
            Stat::make('', $formatToRupiah($income))
                ->description('Income') 
                ->descriptionIcon('heroicon-m-arrow-trending-down') 
                ->color('success'),

            Stat::make('', $formatToRupiah($expense))
                ->description('Expense') 
                ->descriptionIcon('heroicon-m-arrow-trending-up') 
                ->color('danger'),
                
            Stat::make('', $formatToRupiah($cashFlow))
                ->description('Total cash')
                ->descriptionIcon('heroicon-m-banknotes') 
                ->color($cashFlow >= 0 ? 'success' : 'danger'),
        ];
    }
}
