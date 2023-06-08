<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/admin/login');

Route::get('/test', function () {
    $year = date('Y');

    $monthlyTotalAmounts = Transaction::query()
        ->select(
            DB::raw('SUM(transaction_amount) as total_amount'),
            DB::raw('MONTH(created_at) as month')
        )
        ->whereYear('created_at', $year)
        ->whereBetween(DB::raw('MONTH(created_at)'), [1, 12])
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    foreach ($monthlyTotalAmounts as $monthlyTotal) {
        $totalAmount = $monthlyTotal->total_amount;
        $month = $monthlyTotal->month;

        // Do something with the total amount for the month
        echo "Total amount for month $month: $totalAmount";
    }
});
