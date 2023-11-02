<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use HasFactory;

    protected $casts = ['blocked_at' => 'integer'];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function monthlyBalance()
    {
        $customers = Customer::where('house_id', $this->id)->pluck('id');
        $total = collect();

        Month::all()->each(function ($month) use ($total, $customers) {
            $amount = Subscription::where('month_id', $month->id)
                        ->whereIn('customer_id', $customers)
                        ->sum('balance');

            $total->push($amount);
        });

        return intval($total->sum());
    }

    public function router()
    {
        return $this->belongsTo(Router::class);
    }
}
