<?php

namespace App\Console\Commands;

use App\Models\Session;
use Illuminate\Console\Command;

class CreateSessionEveryYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:session';

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
        $session = Session::where('year', now()->year)->first();

        if ($session) return;

        $session = Session::create([
            'year' => now()->year
        ]);

        session()->put('year', $session->id);

        return 'done';
    }
}
