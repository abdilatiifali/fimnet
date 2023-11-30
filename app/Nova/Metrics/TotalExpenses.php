<?php

namespace App\Nova\Metrics;

use App\Models\Expense;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class TotalExpenses extends Value
{
    public function name()
    {
        $currentMonth = now()->format('F');

        return "Total Expenses For ${currentMonth}";
    }

    /**
     * Calculate the value of the metric.
     *
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $currentMonth = intval(
            ltrim(now()->format('m'), '0')
        );

        $amount = Expense::where('month_id', $currentMonth)->sum('amount');

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
        return 'total-expenses';
    }
}
