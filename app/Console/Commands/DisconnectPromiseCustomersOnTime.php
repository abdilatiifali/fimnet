<?php

namespace App\Console\Commands;

use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Models\Router;
use App\Models\Subscription;
use App\Network\ApiRouter;
use Illuminate\Console\Command;

class DisconnectPromiseCustomersOnTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disconnect:promise-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disconnect Customers Who Promise To Pay On Time';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $customerIds = Customer::where('status', CustomerStatus::active->value)
            ->where('block_day', now()->day)->pluck('id');

        $customerIds = Subscription::whereIn('customer_id', $customerIds)
            ->where('amount', '>', 0)
            ->where('paid', false)
            ->where('session_id', config('app.year'))
            ->where('month_id', now()->month)
            ->pluck('customer_id');

        Customer::whereIn('id', $customerIds)
            ->each(function ($customer) {
                ApiRouter::make(Router::findOrFail($customer->router_id))
                    ->openServer()
                    ->disconnectBy($customer);
            });

        return 'done';

    }
}
