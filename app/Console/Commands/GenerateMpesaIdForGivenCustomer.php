<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class GenerateMpesaIdForGivenCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generateMpesa:byId {customerId} ';

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
        Customer::where('id', $this->argument('customerId'))->update([
            'mpesaId' => generateOrderedString(confg('app.mpesa_prefix')),
        ]);

        return 'done';
    }
}
