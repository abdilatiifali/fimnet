<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Subscription;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function index($customerId)
    {
        $logo = asset('fimnet-logo.png');

        $customer = Customer::findOrFail($customerId);

        $shortCode = $customer->house->district->paybill_number;

        $subscriptions = Subscription::where('customer_id', $customer->id)
            ->get();

        $groupedSubscriptions = $subscriptions->groupBy('session_id');

        $pdf = Pdf::loadView('statement', compact('logo', 'customer', 'shortCode', 'groupedSubscriptions'));

        return $pdf->stream('statement.pdf');
    }
}
