<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class PaidType extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->where('paid', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @return array
     */
    public function options(Request $request)
    {
        return [
            'Paid' => 1,
            'Not Paid' => 0,
        ];
    }
}
