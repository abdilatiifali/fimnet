<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class LastThirdayDaysRowUpdated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'last:updated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Latest updated';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->table(
            ['CUSTOMER ID', 'Amount Paid'],
            Subscription::orderBy('updated_at', 'desc')->get(['customer_id', 'amount_paid'])->take(10)->toArray()
        );
    }
}
