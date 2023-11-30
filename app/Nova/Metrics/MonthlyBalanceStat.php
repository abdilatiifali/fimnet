<?php

namespace App\Nova\Metrics;

use App\Models\Month;
use App\Models\Subscription;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class MonthlyBalanceStat extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        [, , $id] = explode('/', $request->path());

        $balance = Subscription::query()
            ->where('month_id', Month::find($id)->id)
            ->where('session_id', session('year'))
            ->sum('balance');

        return $this->result($balance)
            ->allowZeroResult()
            ->format('0,0');
    }

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
        return 'monthly-balance-stat';
    }
}
