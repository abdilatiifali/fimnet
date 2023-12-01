<?php

namespace App\Nova\Metrics;

use App\Models\Income;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Nova;
use Laravel\Nova\Metrics\TrendResult;

class IncomeRevenue extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return (new TrendResult)
            ->trend($this->expectPerMonth())
            ->showSumValue()
            ->format('0,0');
    }

    public function expectPerMonth()
    {
        $total = collect();

        $incomes = Income::where('month_id', now()->month);

        return $total->put(now()->month, $incomes->sum('amount_paid'))->toArray();

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
