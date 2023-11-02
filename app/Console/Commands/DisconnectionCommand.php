<?php

namespace App\Console\Commands;

use App\Models\Router;
use App\Models\Session;
use App\Models\Subscription;
use App\Network\ApiRouter;
use Illuminate\Console\Command;

class DisconnectionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disconnect:allCustomers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disconnect internet from customers that didnt pay';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routers = Router::with('customers')->get();
        $session = Session::where('year', now()->year)->firstOrFail();

        foreach ($routers as $router) {
            $api = ApiRouter::make($router);

            try {
                $client = $api->openServer();
            } catch(\Exception $e) {
                \Log::info('caught it ');

                continue;
            }

            $customerIds = Subscription::whereIn('customer_id', $router->customers->pluck('id'))
                    ->where('amount', '>', 0)
                    ->where('paid', false)
                    ->where('session_id', $session->id)
                    ->where('month_id', now()->month)
                    ->pluck('customer_id');

            $api->disconnect($client, $customerIds);
        }

        info('done');
    }
}
