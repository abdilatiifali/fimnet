<?php

namespace App\Jobs;

use App\Enums\CustomerStatus;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewMonthlyCustomers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $month;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($month)
    {
        $this->month = $month;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $customers = Customer::where('status', '!=', CustomerStatus::blocked->value)
            ->where('amount', '>', 0)
            ->lazy();

        foreach ($customers as $customer) {
            if (! $customer->subscriptions->contains($this->month)) {
                $customer->subscriptions()->attach($this->month, [
                    'amount' => $customer->amount,
                    'balance' => $customer->amount,
                    'session_id' => config('app.year'),
                ]);
            }

            $customer->status = CustomerStatus::active->value;
            $customer->saveQuietly();
        }
    }
}
