<?php

namespace App\Providers;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerSubscriptionUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pivot;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($pivot)
    {
        $this->pivot = $pivot;
    }
}
