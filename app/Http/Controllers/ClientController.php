<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $package = auth()->user()->package;
        $customer = auth()->user();
        $subscriptions = Subscription::where('customer_id', auth()->id())
                    ->where('session_id', session('year'))
                    ->get();

        return view('client/dashboard', compact('subscriptions', 'package', 'customer'));
    }

    public function payment()
    {
        $customer = auth()->user();

        return view('client/payments', compact('customer'));
    }
}
