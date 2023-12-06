<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('disconnect:customers')
            ->dailyAt('9:30');

        $schedule->command('send:payment-reminder')
            ->dailyAt('11:00');

        $schedule->command('disconnect:today')
            ->dailyAt('13:40');

        $schedule->command('sms:due-date')
            ->dailyAt('13:30');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
