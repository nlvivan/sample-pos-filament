<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Models\Transaction;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;

class TransactionLineChart extends LineChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {

        $monthlyTotalAmounts = Transaction::query()
            ->select(
                DB::raw('SUM(transaction_amount) as total_amount'),
                DB::raw('MONTH(created_at) as month')
            )
            ->whereRaw('MONTH(created_at) BETWEEN 1 AND 12')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return $monthlyTotalAmounts;

        return [
            'datasets' => [
                [
                    'label' => 'Sales By Month',
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }
}
