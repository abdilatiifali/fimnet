<?php

namespace App\Nova\Metrics;

use App\Models\Quotation;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Nova;

class QuotationRevenue extends Value
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $lineItems = Quotation::pluck('line_items');

        $totalAmount = 0;
        
        foreach($lineItems as $items) {
            foreach($items as $item) {
                $totalAmount += $item['amount'];
            }
        }

        return $this->result($totalAmount)
            ->allowZeroResult()
            ->format('0,0');

        // return $this->sum($request, Quotation::class, 'amount');
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
        ];
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
