<?php

namespace App\Nova;

use App\Nova\Filters\CustomerFilter;
use App\Nova\Filters\HouseFilter;
use App\Nova\Filters\RouterFilter;
use App\Nova\Metrics\IncomeRevenue;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Income extends Resource
{
    public static $sort = [
        'id' => 'desc'
    ];
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Income>
     */
    public static $model = \App\Models\Income::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'account_number';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'account_number',
        'code',
        'transaction_time',
    ];

    public static function indexQuery(NovaRequest $request, $query)
    {
        if (empty($request->get('orderBy'))) {
            $query->getQuery()->orders = [];

            return $query
                ->where('session_id', session('year'))
                ->orderBy(key(static::$sort), reset(static::$sort));
        }

        return $query->where('session_id', session('year'));
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Username', 'account_number')->sortable(),
            Text::make('Transaction Code', 'code'),
            Text::make('Amount Paid', 'amount_paid')->sortable(),
            Text::make('Balance'),
            Text::make('Excess Amount', 'excess_amount'),
            DateTime::make('Transaction Time', 'transaction_time')->sortable(),
            Text::make('Paid By', 'paid_by'),
            Text::make('Phone Number', 'phone_number'),
            BelongsTo::make('Customer')->searchable()->hideFromIndex(),
        ];
    }
    
    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [
            (new IncomeRevenue)->width('full'),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [
            new CustomerFilter,
            new RouterFilter,
            new HouseFilter,
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
