<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\FromCollection;

class CustomersExport implements FromCollection
{
    public function __construct(public $users)
    {
    }

    public function collection()
    {
        return $this->users;
    }
}
