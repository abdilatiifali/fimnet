<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasFactory, Actionable;

    protected $guarded = [];

    protected $casts = ['blocked_at' => 'datetime'];

    protected $hidden = ['password', 'remember_token'];

    public function subscriptions()
    {
        return $this->belongsToMany(Month::class, 'subscriptions')
            ->where('subscriptions.session_id', session('year'))
            ->withTimestamps()
            ->withPivot(['id', 'paid', 'amount', 'amount_paid', 'payment_type', 'balance', 'session_id', 'updated_at']);
    }

    public function house()
    {
        return $this->belongsTo(House::class);
    }

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function balance()
    {
        return Subscription::where('customer_id', $this->id)
            ->sum('balance');
    }
}
