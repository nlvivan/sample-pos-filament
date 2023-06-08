<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\Transaction;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $transactionNumber = 'OR-'.rand(100000, 999999);
        $transactionData = [];
        $transactionData['transaction_number'] = $transactionNumber;
        $transactionData['transaction_amount'] = 0;
        $transactionData['cash_amount'] = $data['cash_amount'];
        $transactionData['change'] = $data['change'];
        $transactionData['name'] = $data['name'];

        $transaction = Transaction::create($transactionData);

        $orderData = [];
        $totalAmount = 0;

        foreach ($data['fields'] as $key => $field) {
            $totalAmount += $field['total_amount'];
            $obj = [
                'transaction_id' => $transaction->id,
                'product_id' => $field['product_id'],
                'quantity' => $field['quantiy'],
                'total_amount' => $field['total_amount'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            Order::create($obj);
        }

        $transaction->transaction_amount = $totalAmount;
        $transaction->save();

        return $transaction;
    }
}
