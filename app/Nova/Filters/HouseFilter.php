<?php

namespace App\Nova\Filters;

use App\Models\House;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class HouseFilter extends Filter
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
        return $query->where('house_id', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @return array
     */
    public function options(Request $request)
    {
        $houses = House::all();
        $results = collect([]);

        House::all()->each(
            fn ($house) => $results->put($house->name, $house->id)
        );

        return $results;
    }
}
