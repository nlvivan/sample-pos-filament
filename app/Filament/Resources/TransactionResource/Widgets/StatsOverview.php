<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $currentDate = Carbon::now();
        $todaySales = Transaction::whereDate('created_at', $currentDate->format('Y-m-d'))->sum('transaction_amount');
        $thisWeekSales = Transaction::whereBetween('created_at', [$currentDate->startOfWeek()->format('Y-m-d'), $currentDate->endOfWeek()->format('Y-m-d')])->sum('transaction_amount');
        $thisMontSales = Transaction::whereBetween('created_at', [$currentDate->startOfMonth()->format('Y-m-d'), $currentDate->endOfMonth()->format('Y-m-d')])->sum('transaction_amount');

        return [
            Card::make('Sales Today', 'P '.$todaySales),
            Card::make('Sales this Week', 'P '.$thisWeekSales),
            Card::make('Sales this Month', 'P '.$thisMontSales),
        ];
    }
}
