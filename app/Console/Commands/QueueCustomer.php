<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Router;
use App\Network\ApiRouter;
use Illuminate\Console\Command;

class QueueCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:customer {routerId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $router = Router::findOrFail($this->argument('routerId'));

        $customers = Customer::where('router_id', $router->id)->get();

        $api = ApiRouter::make($router);
        $client = $api->openServer();

        foreach ($customers as $customer) {
            $api->queueCustomer($client, $customer);
        }

        return 'done';
    }
}
