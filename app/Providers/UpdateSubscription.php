<?php

namespace App\Providers;

use App\Jobs\ReconnectCustomer;
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
     * @return void
     */
    public function handle(CustomerSubscriptionUpdated $event)
    {
        $customer = Customer::findOrFail($event->pivot->customer_id);

        if (! $customer->router_id) return;

        if (! $event->pivot->amount_paid > 0) return;


        ReconnectCustomer::dispatch($customer);

        if ($customer->due_date && optional($customer->house)->block_day !== now()->day) {
            $customerDueDate = $customer->due_date;
            $now = now();
            $remainingDays = $now->diffInDays($customerDueDate, false);

            $customer->due_date = $remainingDays > 0 
                            ? $customerDueDate->addMonths(1)->format('d-M-Y')
                            : $now->addMonths(1)->format('d-M-Y');

            $customer->saveQuietly();
        }

        return null;
    }
}
