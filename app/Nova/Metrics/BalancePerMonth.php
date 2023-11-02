<?php

namespace App\Nova\Metrics;

use App\Models\Customer;
use App\Models\Month;
use App\Models\Subscription;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class BalancePerMonth extends Trend
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

        return (new TrendResult)
            ->trend($this->balancePerMonth($house))
            ->showCurrentValue()
            ->format('0,0');
    }

    public function balancePerMonth($house)
    {
        $months = Month::all();
        $balance = collect([]);

        $months->each(function ($month) use ($balance, $house) {
            $subscriptions = Subscription::where('session_id', session('year'))
                ->where('month_id', $month->id);

            if ($house) {
                $subscriptions->whereIn(
                    'customer_id',
                    Customer::where('house_id', $house)->pluck('id')
                );
            }

            $balance->put($month->month, $subscriptions->sum('balance'));
        });

        return $balance->toArray();
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addHours(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'balance-per-month';
    }
}
