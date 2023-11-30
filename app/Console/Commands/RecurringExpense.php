<?php

namespace App\Console\Commands;

use App\Models\Expense;
use Illuminate\Console\Command;

class RecurringExpense extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring:expense';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recurring Expenses that should begin everymonth';

    public function recurring()
    {
        $expenses = [
            'internet fee' => 400000,
            'Rent Office' => 50000,
            'Salaries' => 100000,
            'House rent & Food' => 150000,
            'Som Bill' => 60000,
            'Offers & Company Needs' => 65000,
            'Ayuto' => 200000,
        ];

        return $expenses;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expenses = $this->recurring();

        foreach ($expenses as $title => $amount) {
            Expense::create([
                'title' => $title,
                'amount' => $amount,
                'month_id' => now()->month,
            ]);
        }

        return 'Done';
    }
}
