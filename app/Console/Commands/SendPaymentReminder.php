<?php

namespace App\Console\Commands;

use App\Enums\CustomerStatus;
use App\Jobs\SendSms;
use App\Models\Customer;
use App\Models\House;
use App\Models\Session;
use App\Models\SmsGateway;
use App\Models\Subscription;
use Illuminate\Console\Command;

class SendPaymentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:payment-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Payment Reminder to clients';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $session = Session::where('year', now()->year)->firstOrFail();
        $houseIds = House::where(
            'block_day', now()->tomorrow()->day
        )->pluck('id');

        $batchSize = 20;

        $customerIds = Customer::whereIn('house_id', $houseIds)->pluck('id');

        $customerIds = Subscription::whereIn('customer_id', $customerIds)
                    ->where('amount', '>', 0)
                    ->where('paid', false)
                    ->where('session_id', $session->id)
                    ->where('month_id', now()->month)
                    ->pluck('customer_id');

       Customer::whereIn('id', $customerIds)
            ->where('status', CustomerStatus::active->value)
            ->chunk($batchSize, function ($customers) use ($batchSize) {
                SendSms::dispatch($customers, $batchSize);
            });
    }
}
