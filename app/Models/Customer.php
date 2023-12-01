<?php

namespace App\Models;

use App\Models\Income;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Nova\Actions\Actionable;

class Customer extends Authenticatable
{
    use Actionable, HasFactory;

    protected $guarded = [];

    protected $casts = ['blocked_at' => 'datetime', 'due_date' => 'date'];

    protected $hidden = ['password'];

    protected $appends = ['balance'];

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

    public function getBalanceAttribute()
    {
        return $this->balance();
    }

    public function balance()
    {
        return Subscription::where('customer_id', $this->id)
            ->sum('balance');
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function defaultProfilePhotoUrl()
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
