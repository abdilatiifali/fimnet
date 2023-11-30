<?php

namespace App\Nova\Metrics;

use App\Models\Customer;
use App\Models\House;
use App\Models\Month;
use App\Models\Subscription;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class DistrictRevenueMoney extends Trend
{
    public function calculate(NovaRequest $request)
    {
        [, , $districtId] = explode('/', $request->path());

        return (new TrendResult)
            ->trend($this->expectPerMonth($districtId))
            ->showCurrentValue()
            ->format('0,0');
    }

    public function expectPerMonth($districtId)
    {
        $houses = House::where('district_id', $districtId)->pluck('id');

        $customers = Customer::whereIn('house_id', $houses)->pluck('id');

        $total = collect([]);

        Month::all()->each(function ($month) use ($total, $customers) {
            $amount = Subscription::where('session_id', session('year'))
                ->where('month_id', $month->id)
                ->whereIn('customer_id', $customers)
                ->sum('amount_paid');

            $total->put($month->month, $amount);
        });

        return $total->toArray();
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    // public function cacheFor()
    // {
    //     return now()->addHour();
    // }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'district-revenue-money';
    }
}
