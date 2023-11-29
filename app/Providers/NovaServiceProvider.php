<?php

namespace App\Providers;

use App\Nova\Dashboards\Main;
use App\Nova\Metrics\BalancePerMonth;
use App\Nova\Metrics\CustomersPerDay;
use App\Nova\Metrics\ExpectPerMonth;
use App\Nova\Metrics\NewCustomer;
use App\Nova\Metrics\RevenuePerMonth;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Illuminate\Support\Facades\Blade;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::footer(function ($request) {
            return Blade::render('
                @env(\'prod\')
                    This is production!
                @endenv
            ');
        });
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                'abdilatiifali@gmail.com',
                'mohamed.abdi@fimnet.co.ke',
            ]);
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            (new NewCustomer)->width('1/2'),
            (new CustomersPerDay)->width('1/2'),
            new ExpectPerMonth,
            new RevenuePerMonth,
            new BalancePerMonth,
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [new Main];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
