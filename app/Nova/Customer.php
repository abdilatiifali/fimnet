<?php

namespace App\Nova;

use App\Enums\CustomerStatus;
use App\Enums\PaymentType;
use App\Nova\Actions\DisconnectCustomer;
use App\Nova\Actions\ReconnectCustomers;
use App\Nova\Actions\SendCustomerInvoice;
use App\Nova\Actions\SendCustomerStatement;
use App\Nova\Actions\SendPaymentReminder;
use App\Nova\Actions\SendSms;
use App\Nova\Filters\HouseFilter;
use App\Nova\Filters\PaidType;
use App\Nova\Filters\PaymentTypeFilter;
use App\Nova\Filters\StatusType;
use App\Nova\Metrics\ActiveCustomers;
use App\Nova\Metrics\BalancePerCustomer;
use App\Nova\Metrics\NewCustomer;
use App\Nova\Metrics\TotalCustomers;
use Illuminate\Http\Request;
use Inspheric\Fields\Indicator;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Password;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class Customer extends Resource
{
    public static $perPageOptions = [25, 50, 100];

    public static $perPageViaRelationship = 20;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Customer::class;

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
        'name',
        'appartment',
        'ip_address',
        'comment',
        'amount',
        'phone_number',
        'mpesaId',
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

            Badge::make('Status')->map([
                CustomerStatus::active->value => 'success',
                CustomerStatus::blocked->value => 'danger',
                CustomerStatus::new->value => 'info',
            ]),

            Text::make('Name'),

            Text::make('Account Number', 'mpesaId')
                ->rules('required'),

            Text::make('Username')
                ->rules('required')
                ->creationRules('unique:customers,username')
                ->updateRules('unique:customers,username,{{resourceId}}'),
            
            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            Text::make('Ip Address', 'ip_address'),
            Text::make('Units', 'appartment'),
            Number::make('Phone Number', 'phone_number'),
            BelongsTo::make('House'),
            BelongsTo::make('Router')->nullable(),
            Currency::make('Amount')->exceptOnForms(),
            BelongsTo::make('Package'),
            Text::make('Mpesa Id', 'mpesaId')->exceptOnForms()->nullable(),
            DateTime::make('Blocked At', 'blocked_at')->nullable()->hideFromIndex()->exceptOnForms(),
            Date::make('Due Date', 'due_date')->nullable(),

            BelongsToMany::make('Month', 'subscriptions')
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
                        DateTime::make('Updated At', 'updated_at')->onlyOnDetail(),
                    ];
                }),

            Text::make('Comment')->hideFromIndex(),
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
            new TotalCustomers,
            (new BalancePerCustomer)
            ->width('full')
            ->onlyOnDetail(),
            new ActiveCustomers,
            new NewCustomer,
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
        return [
            new PaidType,
            new PaymentTypeFilter,
            new HouseFilter,
            new StatusType,
        ];
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
        return [
            new SendPaymentReminder,
            new SendCustomerStatement,
            new SendSms,
            (new ReconnectCustomers)
                ->canSee(function ($request) {
                    return $request->user()->isAdmin();
                })->canRun(function ($request) {
                    return $request->user()->isAdmin();
                }),

            (new DisconnectCustomer)
                ->canSee(function ($request) {
                    return $request->user()->isAdmin();
                })->canRun(function ($request) {
                    return $request->user()->isAdmin();
                }),
        ];
    }
}
