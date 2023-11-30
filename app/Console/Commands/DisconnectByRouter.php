<?php

namespace App\Console\Commands;

use App\Models\Router;
use App\Models\Session;
use App\Models\Subscription;
use App\Network\ApiRouter;
use Illuminate\Console\Command;

class DisconnectByRouter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disconnect:byId {routerId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disconnect Router with Id';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $router = Router::with('customers')->findOrFail($this->argument('routerId'));
        $session = Session::where('year', now()->year)->firstOrFail();

        $customerIds = Subscription::whereIn('customer_id', $router->customers->pluck('id'))
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
