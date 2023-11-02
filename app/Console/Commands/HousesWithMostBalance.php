<?php

namespace App\Console\Commands;

use App\Models\House;
use Illuminate\Console\Command;

class HousesWithMostBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'most:balance-houses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Houses that we owe the most';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $houses = House::all();

        $results = collect();

        foreach ($houses as $house) {
            $results->put($house->name, $house->monthlyBalance())->toArray();
        }

        dd($results->sortDesc()->take(10));
    }
}
