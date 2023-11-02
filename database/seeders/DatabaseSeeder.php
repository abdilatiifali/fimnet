<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Customer::factory(20)->create();

        // foreach(array_column(\App\Enums\Month::cases(), 'name') as $month) {
        //     \App\Models\Month::create(['month' => $month]);
        // }
    }
}
