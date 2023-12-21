<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['transaction_time' => 'datetime'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function month()
    {
        return $this->belongsTo(Month::class);
    }

    public function make($customer, $code, $transactionTime, $name, $transAmount, $phoneNumber, $mpesaId)
    {
        return static::create([
            'code' => $code,
            'transaction_time' => $transactionTime,
            'paid_by' => $name,
            'customer_id' => $customer->id,
            'month_id' => now()->month,
            'amount_paid' => $transAmount,
            'excess_amount' => $this->excessAmount($customer, $transAmount),
            'balance' => $customer->amount - $transAmount,
            'phone_number' => $phoneNumber,
            'account_number' => $mpesaId,
            'router_id' => $customer->router->id ?? null,
            'house_id' => $customer->house->id,
        ]);
    }

    public function excessAmount($customer, $amount)
    {
        return $amount > $customer->amount ? $amount - $customer->amount : 0;
    }

}
