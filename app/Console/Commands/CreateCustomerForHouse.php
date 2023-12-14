<?php

namespace App\Console\Commands;

use App\Enums\CustomerStatus;
use App\Jobs\UpdateSubscribersPerHouse;
use App\Models\Customer;
use App\Models\Month;
use Illuminate\Console\Command;

class CreateCustomerForHouse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthly:subscribers {house} {month}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create monthly subscriptions for a given house and a month';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $month = Month::where('month', $this->argument('month'))->firstOrFail();

        $customers = Customer::with('subscriptions')
            ->where('house_id', $this->argument('house'))
            ->where('status', '!=', CustomerStatus::blocked->value)
            ->where('amount', '>', 0)
            ->get();

        UpdateSubscribersPerHouse::dispatch($customers, $month);

        $this->info('done');

    }
}
