<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    use HasFactory;

    public function houses()
    {
        return $this->hasMany(House::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
