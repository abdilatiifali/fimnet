<?php

namespace App\Console\Commands;

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
        $customers = Customer::where('house_id', $this->argument('house'))->get();

        $customers->each(function ($customer) use ($month) {
            $customer->subscriptions()->attach($month, [
                'amount' => $customer->amount,
                'balance' => $customer->amount,
            ]);
        });

        $this->info('done');
    }
}
