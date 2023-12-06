<?php

namespace App\Nova\Metrics;

use App\Models\Customer;
use App\Models\Month;
use App\Models\Subscription;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;
use Laravel\Nova\Nova;

class PackageBalanceMoney extends Trend
{
    public function calculate(NovaRequest $request)
    {
        [, , $packageId] = explode('/', $request->path());

        return (new TrendResult)
            ->trend($this->expectPerMonth($packageId))
            ->showCurrentValue()
            ->format('0,0');
    }

    public function expectPerMonth($packageId)
    {
        $customers = Customer::where('package_id', $packageId)->pluck('id');

        $total = collect([]);

        Month::all()->each(function ($month) use ($total, $customers) {
            $amount = Subscription::where('session_id', session('year'))
                ->where('month_id', $month->id)
                ->whereIn('customer_id', $customers)
                ->sum('balance');

            $total->put($month->month, $amount);
        });

        return $total->toArray();
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

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'package-balance-money';
    }
}
