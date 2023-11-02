<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;

class CustomersImport implements ToModel
{
    public function __construct(protected $houseId)
    {
    }

    /**
     * @param  array  $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (! isset($row[1]) && ! isset($row[0])) {
            return null;
        }

        return new Customer([
            'name' => $row[1] ?? 'No name',
            'appartment' => $row[2] ?? 'nill',
            'phone_number' => "0{$row[3]}" ?? 'nill',
            'ip_address' => $row[4] ?? 'nil',
            'amount' => is_numeric($row[6]) ? $row[6] : 0,
            'house_id' => intval($this->houseId),
        ]);
    }
}
