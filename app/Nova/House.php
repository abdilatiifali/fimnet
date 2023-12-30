<?php

namespace App\Nova;

use App\Nova\Metrics\BalancePerMonth;
use App\Nova\Metrics\ExpectPerMonth;
use App\Nova\Metrics\RevenuePerMonth;
use App\Nova\Metrics\ThroughCash;
use App\Nova\Metrics\ThroughMpesa;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class House extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\House::class;

    public static $perPageViaRelationship = 100;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'block_day',
        'name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make('Name'),
            BelongsTo::make('District'),
            BelongsTo::make('Router')->nullable(),
            HasMany::make('Customers'),
            Number::make('Block Day', 'block_day'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            (new ExpectPerMonth)->onlyOnDetail(),
            (new RevenuePerMonth)->onlyOnDetail(),
            (new BalancePerMonth)->onlyOnDetail(),
            (new ThroughMpesa)->onlyOnDetail()->width('1/2'),
            (new ThroughCash)->onlyOnDetail()->width('1/2'),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
