<?php

namespace App\Jobs;

use App\Models\Router;
use App\Network\ApiRouter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReconnectCustomer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $customer)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $router = Router::findOrFail($this->customer->router_id);
        ApiRouter::make($router)
            ->openServer()
            ->reconnect($this->customer)
            ->checkFromTheNat($this->customer);

        return;
    }

   
}
