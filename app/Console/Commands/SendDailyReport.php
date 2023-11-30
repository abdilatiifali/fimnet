<?php

namespace App\Console\Commands;

use App\Imports\DailyReportExport;
use App\Models\Transaction;
use Illuminate\Console\Command;

class SendDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Daily Report';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = now()->format('Y-m-d');

        $transactions = Transaction::whereBetween(
            'created_at', [$today.' 00:00:00', now()->format('Y-m-d H:i:s')]
        )->get();

        $fileName = now()->toDateString().'-'.'customers.xlsx';

        \Excel::store(
            new DailyReportExport($transactions), $fileName, 's3'
        );

        return 'done';
    }
}
