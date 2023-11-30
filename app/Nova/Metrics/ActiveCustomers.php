<?php

namespace App\Nova\Metrics;

use App\Models\Customer;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class ActiveCustomers extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, Customer::class, 'status')
            ->label(function ($value) {
                switch ($value) {
                    case 'active':
                        return 'Active Customer';
                    case 'blocked':
                        return 'Blocked Customer';
                    case 'new':
                        return 'New Customer';
                }
            })->colors([
                    'New Customer' => '#f6993f',
                    'Active Customer' => '#22c55e',
                    'Blocked Customer' => '#ef4444',
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
        return 'active-customers';
    }
}
