<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;

class TransactionExport implements FromQuery
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;

    public $transactions;

    public function __construct(Collection $transactions)
    {
        $this->transactions = $transactions;
    }

    public function query()
    {
        $transactions = Transaction::whereKey($this->transactions->pluck('id')->toArray());

        return $transactions;
    }
}
