<?php

namespace App\Console\Commands;

use App\Models\Package;
use App\Models\Customer;
use Illuminate\Console\Command;
use App\Jobs\UpgradeNewPackages;

class ChangeToNewPackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change-new-package';

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
        $packages = Package::all();

        $customerIds = Customer::pluck('id');

        $batchSize = 20;

        Customer::whereIn('id', $customerIds)
            ->chunk($batchSize, function ($customers) use ($batchSize) {
                $customersByRouter = $customers->groupBy('router_id');
                foreach ($customersByRouter as $routerId => $customers) {
                    $this->info("upgrading routerId ID {$routerId}...");
                    UpgradeNewPackages::dispatch($customers, $routerId, $batchSize);
                }
            });

        // foreach($packages as $package) {
        //     $customers = Customer::where('package_id', $package->id)->get();

        //     foreach($customers as $customer) {
        //         $customer->update([
        //             'name' => $customer->name . 'updated',
        //             'amount' => $package->price,
        //             'package_id' => $package->id,
        //         ]);
        //     }
        // }


        return Command::SUCCESS;
    }
}
