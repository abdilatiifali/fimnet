<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\ActiveCustomers;
use App\Nova\Metrics\BalancePerMonth;
use App\Nova\Metrics\ExpectPerMonth;
use App\Nova\Metrics\NewCustomer;
use App\Nova\Metrics\RevenuePerMonth;
use App\Nova\Metrics\TotalCash;
use App\Nova\Metrics\TotalMpesa;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            new NewCustomer,
            (new ActiveCustomers)->width('2/3'),
            new ExpectPerMonth,
            new RevenuePerMonth,
            new BalancePerMonth,
            (new TotalMpesa)->width('2/3'),
            (new TotalCash),
        ];
    }
}
