<?php

namespace App\Providers;

use App\Models\Customer;
use App\Network\ApiRouter;

class UpdateSubscription
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Providers\CustomerSubscriptionUpdated  $event
     * @return void
     */
    public function handle(CustomerSubscriptionUpdated $event)
    {
        $customer = Customer::findOrFail($event->pivot->customer_id);

        if (! $customer->mikrotik_id || ! $event->pivot->amount_paid > 0) {
            return;
        }

        ApiRouter::make($customer->router)
                ->openServer()
                ->reconnect($customer);

        if (optional($customer->house)->block_day !== now()->day) {
            $customer->due_date = now()->addMonth()->format('d-M-Y');
            $customer->saveQuietly();
        }

        return null;
    }
}
