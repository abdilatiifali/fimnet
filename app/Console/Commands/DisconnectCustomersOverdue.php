<?php

namespace App\Console\Commands;

use App\Enums\CustomerStatus;
use App\Jobs\DisconnectCustomers;
use App\Models\Customer;
use App\Models\House;
use App\Models\Session;
use App\Models\SmsGateway;
use App\Models\Subscription;
use Illuminate\Console\Command;

class DisconnectCustomersOverdue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disconnect:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disconnect Customers Who are Overdue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $housesId = House::where('block_day', now()->day)->pluck('id');
        $session = Session::where('year', now()->year)->firstOrFail();

        $customerIds = Customer::whereIn('house_id', $housesId)
                            ->where('status', CustomerStatus::active->value)
                            ->where('due_date', null)
                            ->pluck('id');

        $batchSize = 50;
        $customerIds = Subscription::whereIn('customer_id', $customerIds)
                    ->where('amount', '>', 0)
                    ->where('paid', false)
                    ->where('session_id', $session->id)
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
            
        // SmsGateway::sendStaffMessage($housesId);
    }
}
