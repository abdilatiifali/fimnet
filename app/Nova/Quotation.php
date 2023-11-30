<?php

namespace App\Nova;

use App\Nova\Actions\DownloadQuotation;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Outl1ne\NovaSimpleRepeatable\SimpleRepeatable;

class Quotation extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Quotation>
     */
    public static $model = \App\Models\Quotation::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Company Name', 'name'),
            Text::make('Address'),
            Text::make('Phone Number', 'phone_number'),
            BelongsTo::make('Customer')->searchable()->nullable(),
            SimpleRepeatable::make('LineItems', 'line_items', [
                Text::make('Item'),
                Number::make('Quantity'),
                Text::make('Package'),
                Currency::make('Amount'),
            ])
                ->canAddRows(true)
                ->canDeleteRows(true),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            new DownloadQuotation,
        ];
    }
}
