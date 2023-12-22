<?php

namespace App\Console\Commands;

use App\Imports\IncomeImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class importIncome extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:income';

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
        Excel::import(
            new IncomeImport,
            base_path("files/income/fimnetThree.xlsx"),
        );

        $this->info('done');
    }
}
