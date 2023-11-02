<?php

namespace App\Nova\Metrics;

use App\Models\Subscription;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class PaidUsers extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        [, , $monthId] = explode('/', $request->path());

        return $this->count($request, Subscription::where('month_id', $monthId)
                    ->where('amount', '>', 0)
                    ->where('session_id', session('year')), 'paid', 'month_id')
                ->label(function ($value) {
                    switch($value) {
                        case 0:
                            return 'Not Paid';
                        default:
                            return 'Paid Users';
                    }
                })
                ->colors([
                    'Paid Users' => '#22c55e',
                    'Not Paid' => '#ef4444',
                ]);
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
        return 'paid-users';
    }
}
