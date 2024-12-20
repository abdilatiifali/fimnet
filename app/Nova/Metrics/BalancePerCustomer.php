<?php

namespace App\Nova\Metrics;

use App\Models\Subscription;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class BalancePerCustomer extends Value
{
    public function name()
    {
        return 'Remaining Balance for this customer';
    }

    /**
     * Calculate the value of the metric.
     *
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        [, , $customerId] = explode('/', $request->path());

        $amount = Subscription::where('customer_id', $customerId)->sum('balance');

        return $this->result($amount)
            ->allowZeroResult()
            ->format('0,0');
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    // public function ranges()
    // {
    //     return [
    //         30 => __('30 Days'),
    //         60 => __('60 Days'),
    //         90 => __('90 Days'),
    //     ];
    // }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'balance-per-customer';
    }
}
