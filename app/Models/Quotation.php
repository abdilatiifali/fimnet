<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $casts = ['line_items' => 'array'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
