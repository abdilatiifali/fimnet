<?php

namespace App\Nova\Metrics;

use App\Models\Subscription;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class PaymentType extends Partition
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

        $subscription = Subscription::where('session_id', session('year'))
                            ->where('month_id', $monthId);

        return $this->count($request, $subscription, 'payment_type')
            ->label(function ($value) {
                switch($value) {
                    case 'cash':
                        return 'Cash';
                    case 'mpesa':
                        return 'Mpesa';
                    case 'akram':
                        return 'Akram';
                    default:
                        return;
                }
            })->colors([
                'Cash' => '#21b978',
                'Mpesa' => '#03a9f4',
                'Akram' => '#ffeb3b',
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
        return 'payment-type';
    }
}
