<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class GenerateMpesaAccountNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:mpesaId';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Mpesa Account Number';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Customer::all()->each(function ($customer) {
            if ($customer->amount > 0) {
                $customer->update([
                    'mpesaId' => generate_unique_string(confg('app.mpesa_prefix')),
                ]);
            }
        });
    }
}
