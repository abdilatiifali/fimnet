<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Income;
use App\Models\Subscription;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Enums\PaymentType;

class IncomeImport implements ToModel
{

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $income = Income::where('code', $row[0])->first();

        if ($income) return;

        $customer = Customer::where('mpesaId', $row[1])->first();

        if (!$customer) return;

        $pivot = Subscription::where('customer_id', $customer->id)
                ->where('month_id', now()->month)
                ->where('session_id', config('app.year'))
                ->first();

        $pivot
            ? $this->updateSubscription($customer, $pivot, $row[2])
            : $pivot = $this->createSubscription($customer, $row[2]);


        Income::create([
            'code' => $row[0],
            'transaction_time' => $row[5],
            'paid_by' => $row[6],
            'customer_id' => $customer->id,
            'month_id' => now()->month,
            'amount_paid' => $row[2],
            'excess_amount' => $row[4],
            'balance' => $row[3],
            'phone_number' => $row[7],
            'account_number' => $row[1],
            'router_id' => $customer->router->id ?? null,
            'house_id' => $customer->house->id,
        ]);

    }

    public function updateSubscription($customer, $pivot, $amount)
    {
        return $pivot->update([
            'amount_paid' => $amount + $pivot->amount_paid,
            'payment_type' => 'mpesa',
            'paid' => true,
            'balance' => $pivot->amount - (intval($amount) + $pivot->amount_paid),
        ]);
    }

    public function createSubscription($customer, $transAmount)
    {
        $subscription = Subscription::create([
            'customer_id' => $customer->id,
            'month_id' => now()->month,
            'session_id' => config('app.year'),
            'amount' => $customer->amount,
            'amount_paid' => intval($transAmount),
            'payment_type' => PaymentType::mpesa->value,
            'balance' => $customer->amount - intval($transAmount),
            'paid' => true,
        ]);

        return $subscription;
    }
}
