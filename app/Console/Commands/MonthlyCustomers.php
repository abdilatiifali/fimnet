<?php

namespace App\Console\Commands;

use App\Jobs\NewMonthlyCustomers;
use App\Models\Month;
use Illuminate\Console\Command;

class MonthlyCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthly:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monthly create new customers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $month = Month::findOrFail(now()->month);

        NewMonthlyCustomers::dispatch($month);

        $this->info('done');
    }
}
