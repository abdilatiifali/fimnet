<?php

namespace App\Console\Commands;

use App\Imports\CustomersImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:customers {house} {houseId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Customers from excel file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Excel::import(
            new CustomersImport($this->argument('houseId')),
            base_path("files/{$this->argument('house')}.xlsx"),
        );

        $this->info('done');
    }
}
