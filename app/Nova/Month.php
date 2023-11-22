<?php

namespace App\Nova;

use App\Enums\PaymentType;
use App\Nova\Metrics\MonthlyBalanceStat;
use App\Nova\Metrics\MonthlyExpectedStat;
use App\Nova\Metrics\MonthlyRevenueStat;
use App\Nova\Metrics\PaidUsers;
use App\Nova\Metrics\PaymentType as PaymentTypeCard;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class Month extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Month::class;

    public static $perPageViaRelationship = 12;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'month';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'month',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make('Month'),

            BelongsToMany::make('Customer', 'subscriptions')
                ->fields(function () {
                    return [
                        Boolean::make('Paid')->readonly(function ($request) {
                            return ! $request->user()->isAdmin();
                        }),

                        Badge::make('payment_type', function ($subscription) {
                            return $subscription->payment_type == null ? '_' : $subscription->payment_type;
                        })->map([
                            'cash' => 'success',
                            'mpesa' => 'info',
                            'akram' => 'warning',
                            '_' => 'danger',
                        ]),
                        Select::make('Payment Type', 'payment_type')
                            ->options([
                                PaymentType::cash->value => 'Cash',
                                PaymentType::mpesa->value => 'Mpesa',
                                PaymentType::_->value => '_',
                            ])
                            ->displayUsingLabels()
                            ->onlyOnForms()
                            ->readonly(function ($request) {
                                return ! $request->user()->isAdmin();
                            }
                        ),

                        Currency::make('Amount')->readonly(function ($request) {
                            return ! $request->user()->isAdmin();
                        }),
                        Currency::make('Amount Paid', 'amount_paid')->readonly(function ($request) {
                            return ! $request->user()->isAdmin();
                        }),
                        Currency::make('Balance')->exceptOnForms(),
                        DateTime::make('Updated At', 'updated_at')->exceptOnForms(),
                    ];
                }),

            // HasMany::make('Expense'),

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            (new MonthlyExpectedStat)->onlyOnDetail(),
            (new MonthlyRevenueStat)->onlyOnDetail(),
            (new MonthlyBalanceStat)->onlyOnDetail(),
            (new PaidUsers)->onlyOnDetail()->width('1/2'),
            (new PaymentTypeCard)->onlyOnDetail()->width('1/2'),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
