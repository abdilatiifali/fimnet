<?php

namespace App\Listeners;

use App\Jobs\AddIpToNat;
use App\Network\ApiRouter;
use App\Providers\CustomerCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CheckIfTheIpIsNated
{

    public function handle(CustomerCreated $event)
    {
        $customer = $event->customer;

        if (! $customer->router || ! filter_var($customer->ip_address, FILTER_VALIDATE_IP)) return;

        ApiRouter::make($customer->router)
            ->openServer()
            ->checkIfTheIpIsNated($customer);

        return 'done';

    }
}
