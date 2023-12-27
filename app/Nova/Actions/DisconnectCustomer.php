<?php

namespace App\Nova\Actions;

use App\Jobs\DisconnectCustomers;
use App\Models\Router;
use App\Network\ApiRouter;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class DisconnectCustomer extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {

        DisconnectCustomers::dispatch($models, $models[0]->router_id);

        // foreach ($models as $customer) {

        //     DisconnectionHandler::
        //     ApiRouter::make(Router::findOrFail($customer->router_id))
        //         ->openServer()
        //         ->disconnectBy($customer);
        // }
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
