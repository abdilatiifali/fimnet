<?php

namespace App\Observers;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

class SubscriptionObserver
{
    public function creating()
    {
        Nova::whenServing(function (NovaRequest $request) {
            info('hello man');
        });
    }
}
