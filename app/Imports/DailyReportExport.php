<?php 

namespace App\Imports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DailyReportExport implements FromCollection, WithHeadings
{
	private $transactions;

	public function __construct($transactions)
	{
		$this->transactions = $transactions;
	}

	public function headings(): array
	{
		 return [
		 	'ID',
            'Name',
            'House',
            'Amount',
            'Payment Type',
        ];
	}

	public function collection()
	{
		return $this->transactions;
	}
}
