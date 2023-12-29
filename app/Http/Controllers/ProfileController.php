<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $customer = auth()->user();
        return view('client.profile', compact('customer'));
    }

    public function store()
    {
        auth()->user()->update([
            'name' => request('name'),
            'phone_number' => request('phone_number'),
        ]);

        if (request('password')) {
            auth()->user()->update(['password' => bcrypt(request('password'))]);
        }

        return back();
    }
}
