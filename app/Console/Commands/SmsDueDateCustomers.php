<?php

namespace App\Console\Commands;

use App\Enums\CustomerStatus;
use App\Jobs\SendSms;
use App\Models\Customer;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SmsDueDateCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:due-date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SMS DUE DATE CUSTOMERS';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $batchSize = 20;
        $date = now()->tomorrow()->format('Y-m-d');

        $customerIds = Customer::where('due_date', $date)->pluck('id');

        $customerIds = Subscription::whereIn('customer_id', $customerIds)
            ->where('amount', '>', 0)
            ->where('paid', false)
            ->where('session_id', config('app.year'))
            ->where('month_id', now()->month)
            ->pluck('customer_id');

        Customer::whereIn('id', $customerIds)
            ->where('status', CustomerStatus::active->value)
            ->chunk($batchSize, function ($customers) use ($batchSize) {
                dd($customers);
                SendSms::dispatch($customers, $batchSize);
            });
    }
}
