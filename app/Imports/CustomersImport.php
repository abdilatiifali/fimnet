<?php

namespace App\Imports;

use App\Models\Customer;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;

class CustomersImport implements ToModel
{
    public function __construct(protected $houseId)
    {
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (! isset($row[1]) && ! isset($row[0])) {
            return null;
        }

        return new Customer([
            'name' => $row[0] ?? 'No name',
            'mpesaId' => $row[1],
            'username' => $row[1],
            'password' => $row[1],
            'due_date' => Carbon::parse(trim($row[2], '"'))->format('d-M-Y'),
            'phone_number' => "0{$row[3]}" ?? 'nill',
            'ip_address' => $row[4] ?? 'nil',
            'amount' => is_numeric($row[5]) ? $row[5] : 0,
            'package_id' => intval($row[6]),
            'house_id' => intval($this->houseId),
        ]);
    }
}
