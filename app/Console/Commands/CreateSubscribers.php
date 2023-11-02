<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class CreateSubscribers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:subscribers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update Subscriptions for customers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $subscripers = Subscription::where('month_id', 5)->get();

        $subscripers->each(function ($subs) {
            $subs->update([
                'balance' => 0,
                'amount_paid' => $subs->amount,
                'paid' => true,
            ]);
        });

        $this->info('done');
    }
}
