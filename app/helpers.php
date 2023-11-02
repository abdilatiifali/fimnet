<?php

use Illuminate\Support\Str;

function generate_unique_string($prefix)
{
    static $count = 1;

    return $prefix.sprintf('%03d', $count++);
}

function generateOrderedString($prefix = null, $padLength = 3, $start = 1)
{
    $prefix = $prefix ?? config('app.mpesa_prefix');

    $mpesaCode = \DB::table('customers')
                ->select('mpesaId')
                ->selectRaw('CAST(SUBSTRING(mpesaId, 3) AS UNSIGNED) AS numeric_id')
                ->orderByRaw('numeric_id DESC')
                ->latest()
                ->first();


    if (! $mpesaCode) {
        return generate_unique_string($prefix);
    }

    $code = Str::substr($mpesaCode->mpesaId, 2);

    // next code
    return $prefix.Str::padLeft(++$code, 3, 0);
}
