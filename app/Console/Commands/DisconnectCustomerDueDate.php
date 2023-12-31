<?php

namespace App\Console\Commands;

use App\Enums\CustomerStatus;
use App\Jobs\DisconnectCustomers;
use App\Models\Customer;
use App\Models\Router;
use App\Models\Subscription;
use App\Network\ApiRouter;
use Illuminate\Console\Command;

class DisconnectCustomerDueDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disconnect:today';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disconnect Customers who are disconneced today';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $customerIds = Customer::where('status', CustomerStatus::active->value)
            ->where('due_date', now()->toDateString())->pluck('id');

        $batchSize = 50;

        $customerIds = Subscription::whereIn('customer_id', $customerIds)
            ->where('amount', '>', 0)
            ->where('paid', false)
            ->where('session_id', config('app.year'))
            ->where('month_id', now()->month)
            ->pluck('customer_id');

         Customer::whereIn('id', $customerIds)
            ->chunk($batchSize, function ($customers) use ($batchSize) {
                $customersByRouter = $customers->groupBy('router_id');
                foreach ($customersByRouter as $routerId => $customers) {
                    $this->info("Blocking IP addresses for router ID {$routerId}...");
                    DisconnectCustomers::dispatch($customers, $routerId, $batchSize);
                    $this->info("IP addresses blocked for router ID {$routerId}");
                }
            });

        return info('done');
    }
}
