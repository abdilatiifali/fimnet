<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Package;
use Illuminate\Http\Request;

class ServiceControler extends Controller
{
    public function index()
    {
        $package = auth()->user()->package;

        return view('client.services', [
            'customer' => auth()->user()->load('package'),
            'packages' => Package::where('price', '>', $package->price)
                                ->get()
        ]);
    }

    public function store()
    {
        $newPackage = Package::findOrFail(request('current-package'));

        auth()->user()->update([
            'package_id' => $newPackage->id,
        ]);

        return redirect("/client");
    }

    public function comparePlan($newPackage, $oldPackage)
    {
        $dayOfMonth = now()->day;
        $oldPrice = $oldPackage->price;
        $newPrice = $newPackage->price;
        $totalDaysInAmonth = now()->daysInMonth;
        $remainingDays = $totalDaysInAmonth - $dayOfMonth + 1; // Adding 1 to include the switch day
        // Daily cost for each plan
        $basicDailyCost = $oldPrice / $totalDaysInAmonth;
        $premiumDailyCost = $newPrice / $totalDaysInAmonth;

        $remainingBasicCost = $remainingDays * $basicDailyCost;
        $remainingPremiumCost = $remainingDays * $premiumDailyCost;

        $proratedDifference = $remainingPremiumCost - $remainingBasicCost;

        return intval(number_format(round($proratedDifference)));
    }
}
