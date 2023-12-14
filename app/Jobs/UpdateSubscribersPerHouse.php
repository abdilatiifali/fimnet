<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Enums\CustomerStatus;

class UpdateSubscribersPerHouse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $customers, public $month)
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
        $month = $this->month;
        $subscriptionsToAttach = $this->customers->filter(function ($customer) use ($month) {
            return !$customer->subscriptions->contains($month);
        })->mapWithKeys(function ($customer) use ($month) {
            return [$customer->id => [
                'amount' => $customer->amount,
                'balance' => $customer->amount,
                'session_id' => config('app.year'),
            ]];
        });

        $this->customers->each(function ($customer) use ($month, $subscriptionsToAttach) {
            $customer->subscriptions()->syncWithoutDetaching([
                $month->id => $subscriptionsToAttach[$customer->id] ?? [],
            ]);
            
            $customer->status = CustomerStatus::active->value;
            $customer->saveQuietly();
        });

    }
}
