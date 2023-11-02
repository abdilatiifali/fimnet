<?php

namespace App\Nova\Metrics;

use App\Models\Customer;
use App\Models\Month;
use App\Models\Subscription;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

class RevenuePerMonth extends Trend
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
            ->trend($this->revenuePerMonth($house))
            ->showCurrentValue()
            ->format('0,0');
    }

   public function revenuePerMonth($house = null)
   {
       $months = Month::all();
       $revenue = collect([]);

       $months->each(function ($month) use ($revenue, $house) {
           $subscriptions = Subscription::where('session_id', session('year'))
               ->where('month_id', $month->id);

           if ($house) {
               $subscriptions->whereIn(
                   'customer_id',
                   Customer::where('house_id', $house)->pluck('id')
               );
           }

           $revenue->put($month->month, $subscriptions->sum('amount_paid'));
       });

       return $revenue->toArray();
   }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    // public function cacheFor()
    // {
    //     return now()->addHours(5);
    // }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'revenue-per-month';
    }
}
