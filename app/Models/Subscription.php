<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Laravel\Nova\Actions\Actionable;

class Subscription extends Pivot
{
    use Actionable, HasFactory;

    protected $table = 'subscriptions';

    protected $guarded = [];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }
}
