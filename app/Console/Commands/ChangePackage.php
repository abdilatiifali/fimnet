<?php

namespace App\Console\Commands;

use App\Jobs\UpgradePackage;
use App\Models\Customer;
use App\Models\House;
use App\Models\Package;
use App\Models\Subscription;
use Illuminate\Console\Command;

class ChangePackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:package {houseId} {packageId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $house = House::findOrFail($this->argument('houseId'));

        $package = Package::findOrFail($this->argument('packageId'));

        $customerIds = Customer::where('house_id', $house->id)
                            ->where('package_id', '!=', 1)
                            ->pluck('id');


        Customer::whereIn('id', $customerIds)->chunk(20, function ($customers) use ($package) {
            UpgradePackage::dispatch($customers, $package);
            // foreach($customers as $customer) {
            //     $customer->update(['package_id' => $package->id]);
            // }
        });

        // foreach($customers as $customer) {
        //     $customer->update(['package_id' => $package->id]);
        // }

        // Subscription::whereIn('customer_id', $customerIds)
        //         ->where('session_id', config('app.year'))
        //         ->where('month_id', now()->month)
        //         ->each(function ($pivot) use ($package) {
        //             if (! $pivot->paid ) {
        //                 $pivot->update([
        //                     'amount' => $package->price,
        //                     'balance' => $package->price,
        //                 ]);
        //             }
        //         });

        // return Command::SUCCESS;
    }
}
