<?php

use App\Http\Controllers\PaymentsController;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\TicketResouce;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
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

Route::get('/client', function () {
    return CustomerResource::make(
       auth()->user()->load('subscriptions')
    );
})->middleware('auth:sanctum');

Route::get("/profile", function () {
    $customer = auth()->user();
    return response()->json([
        'name' => $customer->name,
        'phoneNumber' => $customer->phone_number,
    ], 200);
})->middleware('auth:sanctum');


Route::get("/services", function () {
    $package = auth()->user()->package;

    return response()->json([
        'currentPackage' => $package,
        'packages' => Package::where('price', '>', $package->price)
                            ->get()
     ]);
})->middleware('auth:sanctum');

Route::post("/services", function () {
    $customer = auth()->user();

    $newPackage = Package::findOrFail(request('serviceId'));

    $customer->update([
        'package_id' => $newPackage->id,
    ]);

    return response()->json([
        'currentPackage' => $customer->fresh()->package,
        'packages' => Package::where('price', '>', $newPackage->price)
                        ->get()
    ], 200);

})->middleware('auth:sanctum');


Route::post('/profile', function () {
    $customer = auth()->user();

    $customer->update([
        'name' => request('name'),
        'phone_number' => request('phoneNumber'),
    ]);

    if (request('password')) {
        auth()->user()->update(['password' => bcrypt(request('password'))]);
    }

    return response()->json([
        'name' => $customer->fresh()->name,
        'phoneNumber' => $customer->fresh()->phone_number,
    ], 200);

})->middleware('auth:sanctum');

Route::post('/payment', [PaymentsController::class, 'stkdpush'])->middleware('auth:sanctum');


Route::get('/user', function (Request $request) {
    return response()->json([
        'name' => \Auth::user()->name,
        'photo' => \Auth::user()->defaultProfilePhotoUrl(),
    ]);
})->middleware(['auth:sanctum']);

Route::post('/login', function (Request $request) {
    $input = $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    $user = Customer::where('username', $request->username)->first();
    
    if (! \Auth::guard('api')->attempt($input)) {
        return response()->json([
            'message' => 'The crediantials is wrong',
        ], 422);
    }

    $token = $user->createToken($request->device_name)->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => $user->only('id', 'name', 'username'),
    ], 201);
});

Route::post('/logout', function (Request $request) {
    $user = $request->user()->currentAccessToken()->delete();
    return response()->json([
        'Message' => 'Logedout',
    ], 200);
})->middleware('auth:sanctum');


Route::get('/tickets', function () {
    $customer = auth()->user();

    return response()->json([
        'tickets' => TicketResouce::collection(
            $customer->tickets()->latest()->take(10)->get()
        )
    ], 200);
})->middleware('auth:sanctum');

Route::post('/tickets', function () {
    $customer = auth()->user();

    $ticket = Ticket::create([
        'title' => request('title'),
        'descriptions' => request('description'),
        'customer_id' => $customer->id,
    ]);

    return response()->json([
        'id' => $ticket->id,
        'title' => $ticket->title,
        'customer' => $ticket->fresh()->customer->name,
        'createdAt' => $ticket->created_at->diffForHumans(),
    ], 201);
})->middleware('auth:sanctum');
