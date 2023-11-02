<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class AssignCustomersToRouter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:customers {houseId} {routerId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assing customers to routers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $customerIds = Customer::where('house_id', $this->argument('houseId'))->pluck('id')->toArray();

        $routerId = $this->argument('routerId');

        $updated = \DB::update("
            update customers
            set router_id = '$routerId'
            where id in (".implode(',', $customerIds).')
        ');

        info('Done');
    }
}
