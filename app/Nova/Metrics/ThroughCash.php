<?php

namespace App\Nova\Metrics;

use App\Models\Customer;
use App\Models\Subscription;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Metrics\TrendResult;
use App\Enums\PaymentType;

class ThroughCash extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        [, , $houseId] = explode('/', $request->path());
        $house = null;
        if (intval($houseId) > 0) {
            $house = intval($houseId);
        }


        return $this->result($this->expectPerMonth($house))
            ->allowZeroResult()
            ->format('0,0');
    }

    public function expectPerMonth($house)
    {
        return Subscription::whereIn('customer_id', Customer::where('house_id', $house)->pluck('id'))
            ->where('session_id', session('year'))
            ->where('month_id', now()->month)
            ->where('payment_type', PaymentType::cash->value)
            ->sum('amount_paid');
    }
    

    /**
     * Determine the amount of time the results of the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }
}
