<?php

namespace App\Jobs;

use App\Models\Package;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpgradePackage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $customers, public Package $package)
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
        $package = $this->package;

        foreach($this->customers as $customer) {
            $customer->update(['package_id' => $package->id]);
        }

       $customerIds = $this->customers->pluck('id');

        Subscription::whereIn('customer_id', $customerIds)
                ->where('session_id', config('app.year'))
                ->where('month_id', now()->month)
                ->each(function ($pivot) use ($package) {
                    if (! $pivot->paid ) {
                        $pivot->update([
                            'amount' => $package->price,
                            'balance' => $package->price,
                        ]);
                    }
                });
    }
}
