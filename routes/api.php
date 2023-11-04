<?php

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/customers/{houseId}', function (Request $request, $houseId) {
    $customers = Customer::select('name', 'phone_number', 'appartment', 'ip_address', 'mpesaId')
                    ->where('house_id', $houseId)
                    ->get();

    return $customers;
});

Route::get("/client", function () {
    $customer = CustomerResource::make(
        Customer::findOrFail(1)->load('subscriptions')
    );
    return $customer;
})->middleware('auth:sanctum');
