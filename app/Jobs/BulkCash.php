<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Providers\CustomerSubscriptionUpdated;
use App\Enums\PaymentType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BulkCash implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $customer)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pivot = Subscription::where('customer_id', $this->customer->id)
                ->where('month_id', now()->month)
                ->where('session_id', config('app.year'))
                ->first();

        $pivot
            ? $this->updateSubscription($this->customer, $pivot)
            : $pivot = $this->createSubscription($this->customer);


        return $pivot;

        // event(new CustomerSubscriptionUpdated($pivot));
    }

    public function updateSubscription($customer, $pivot)
    {
        $pivot->update([
            'amount_paid' => $pivot->amount,
            'payment_type' => PaymentType::cash->value,
            'balance' => 0,
            'paid' => true,
        ]);

        $this->updateDueDate($customer);
    }

    public function createSubscription($customer)
    {
        $pivot = Subscription::create([
            'customer_id' => $customer->id,
            'month_id' => now()->month,
            'session_id' => config('app.year'),
            'amount' => $customer->amount,
            'amount_paid' => $customer->amount,
            'payment_type' => PaymentType::cash->value,
            'balance' => 0,
            'paid' => true,
        ]);

        $this->updateDueDate($customer);

        return $pivot;
    }

    public function updateDueDate($customer)
    {
        if ($customer->due_date && optional($customer->house)->block_day !== now()->day) {
            $customerDueDate = $customer->due_date;
            $now = now();
            $remainingDays = $now->diffInDays($customerDueDate, false);

            $customer->due_date = $remainingDays > 0 
                            ? $customerDueDate->addMonths(1)->format('d-M-Y')
                            : $now->addMonths(1)->format('d-M-Y');

            $customer->saveQuietly();
        }
    }
}
