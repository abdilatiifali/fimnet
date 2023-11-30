<?php

namespace App\Listeners;

class SendCustomerCrediantials
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        dd(decrypt($event->password));
    }
}
