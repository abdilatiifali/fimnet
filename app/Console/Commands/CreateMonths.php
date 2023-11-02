<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateMonths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:months';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create All Months for this year';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (array_column(\App\Enums\Month::cases(), 'name') as $month) {
            \App\Models\Month::create(['month' => $month]);
        }

        $this->info('Done');
    }
}
