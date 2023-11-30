<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Subscription;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function index($customerId)
    {
        $logo = asset('aflax-logo.jpeg');
        $customer = Customer::findOrFail($customerId);

        $subscriptions = Subscription::where('customer_id', $customer->id)
            ->get();

        $groupedSubscriptions = $subscriptions->groupBy('session_id');

        $pdf = Pdf::loadView('statement', compact('logo', 'customer', 'groupedSubscriptions'));

        return $pdf->stream('statement.pdf');
    }
}
