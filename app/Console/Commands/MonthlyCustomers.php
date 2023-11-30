<?php

namespace App\Console\Commands;

use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Models\Month;
use Illuminate\Console\Command;

class MonthlyCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthly:customers {month}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monthly create new customers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $month = Month::where('month', $this->argument('month'))->firstOrFail();

        $customers = Customer::where('status', '!=', CustomerStatus::blocked->value)
            ->where('amount', '>', 0)
            ->lazy();

        foreach ($customers as $customer) {
            if (! $customer->subscriptions->contains($month)) {
                $customer->subscriptions()->attach($month, [
                    'amount' => $customer->amount,
                    'balance' => $customer->amount,
                    'session_id' => 1,
                ]);
            }

            $customer->update(['status' => CustomerStatus::active->value]);
        }

        $this->info('done');
    }
}
