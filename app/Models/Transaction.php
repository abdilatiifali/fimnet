<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    public static function record($pivot) 
    {
        $customer = Customer::findOrFail($pivot->customer_id);

        static::create([
            'name' => $customer->name,
            'unit' => $customer->house?->name . ' ' . $customer?->appartment ?? 'null',
            'amount' => $pivot->amount_paid,
            'payment_type' => $pivot->payment_type,
        ]);
    }
}
