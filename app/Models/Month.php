<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;

class Month extends Model
{
    use HasFactory, Actionable;

    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function subscriptions()
    {
        return $this->belongsToMany(Customer::class, 'subscriptions')
                ->where('session_id', session('year'))
                ->withPivot(['id', 'paid', 'amount', 'payment_type', 'amount_paid', 'session_id', 'balance', 'updated_at'])
                ->withTimestamps();
    }

    public function expense()
    {
        return $this->hasMany(Expense::class);
    }

    public function sessions()
    {
        return $this->belongsToMany(Session::class);
    }
}
