<?php

namespace App\Console\Commands;

use App\Models\House;
use App\Models\Router;
use App\Models\Session;
use App\Models\Subscription;
use App\Network\ApiRouter;
use Illuminate\Console\Command;

class DisconnectCustomersByHouse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disconnect:byHouseId {houseId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disconnect customers by House';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $house = House::with('customers')->findOrFail($this->argument('houseId'));
        $session = Session::where('year', now()->year)->firstOrFail();

        $router = Router::findOrFail($house->router_id);

        $customerIds = Subscription::whereIn('customer_id', $house->customers->pluck('id'))
            ->where('amount', '>', 0)
            ->where('paid', false)
            ->where('session_id', $session->id)
            ->where('month_id', now()->month)
            ->pluck('customer_id');

        $api = ApiRouter::make($router);

        try {
            $client = $api->openServer($router);
        } catch (\Exception $e) {
            \Log::info('something went wrong');
        }

        $api->disconnect($client, $customerIds);

        info('done');
    }
}
