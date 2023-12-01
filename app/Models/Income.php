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
}
