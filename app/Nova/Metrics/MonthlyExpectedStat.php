<?php

namespace App\Nova\Metrics;

use App\Models\Month;
use App\Models\Subscription;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class MonthlyExpectedStat extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        [, , $id] = explode('/', $request->path());

        $amount = Subscription::query()
                ->where('month_id', Month::find($id)->id)
                ->where('session_id', session('year'))
                ->sum('amount');

        return $this->result($amount)
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
        return 'monthly-expected-stat';
    }
}
