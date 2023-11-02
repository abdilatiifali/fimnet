<?php

namespace App\Providers;

use App\Network\ApiRouter;

class QueueCustomer
{
    public function handle(CustomerCreated $event)
    {
        if (! $event->customer->router || ! filter_var($event->customer->ip_address, FILTER_VALIDATE_IP)) {
            return;
        }

        ApiRouter::make($event->customer->router)
            ->openServer()
            ->queueCustomer($event->customer);
    }
}
