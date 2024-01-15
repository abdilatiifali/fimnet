<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ServiceControler;
use App\Models\Customer;
use App\Models\Month;
use App\Models\Subscription;
use Illuminate\Support\Facades\Route;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

// $url = config('app.url');

Route::domain("client.fimnetplus.net")->group(function(){
    Route::redirect('/', '/login');
    Route::get("/login", [AuthController::class, 'loginForm'])->middleware('guest:api');
    Route::post('/login', [AuthController::class, 'store'])->middleware('guest:api');
    Route::post('/logout', [AuthController::class, 'destory'])->middleware('auth:api');

    Route::get("/client", [ClientController::class, 'index'])->middleware('auth:api');
    Route::get("/payments", [ClientController::class, 'payment'])->middleware('auth:api');
    Route::post('/payments', [PaymentsController::class, 'stkdpush'])->middleware('auth:api');

    Route::get("/services", [ServiceControler::class, 'index'])->middleware('auth:api');
    Route::post("/services", [ServiceControler::class, 'store'])->middleware('auth:api');

    Route::get("/profile", [ProfileController::class, 'index'])->middleware('auth:api');
    Route::post("/profile", [ProfileController::class, 'store'])->middleware('auth:api');

    Route::get("/home", function () {
        return redirect('/client');
    })->middleware('auth:api');
    // Route::get("/")
});

Route::post('/logout', [AuthController::class, 'destory']);
Route::get('/quotations/{id}', [QuotationController::class, 'show']);

Route::get('/statement/{customer}', [PdfController::class, 'index']);

// FIMNET ONE
Route::post('/validation', [PaymentsController::class, 'validation']);
Route::get('/registerUrl', [PaymentsController::class, 'registerUrl']);
Route::post('/confirmation', [PaymentsController::class, 'confirmation']);

// FIMNET TWO
Route::get('/fimnet2RegisterUrl', [PaymentsController::class, 'fimnet2RegisterUrl']);
Route::post('/fimnet2Confirmation', [PaymentsController::class, 'confirmation']);
Route::post('/fimnet2Validation', [PaymentsController::class, 'validation']);

// FIMNET THREE
Route::get('/fimnetThreeRegisterUrl', [PaymentsController::class, 'fimnetThreeRegisterUrl']);
Route::post('/fimnetThreeValidation', [PaymentsController::class, 'validation']);
Route::post('/fimnetThreeConfirmation', [PaymentsController::class, 'confirmation']);

Route::redirect('/', '/admin');

Route::get('/invoice/{id}', function ($id) {
    $model = Customer::findOrFail($id);

    $customer = new Party([
        'name' => $model->name,
        'address' => $model->house->name.' '.$model->appartment,
        'code' => '#22663214',
        'custom_fields' => [
            'Phone number' => $model->phone_number,
        ],
    ]);

    $subscriptions = Subscription::query()
        ->where('customer_id', $model->id)
        ->where('paid', 0)
        ->get();

    $items = [];

    foreach ($subscriptions as $subscription) {
        $month = Month::findOrFail($subscription->month_id);
        array_push(
            $items,
            (new InvoiceItem)->title($month->month)
                ->pricePerUnit($subscription->balance)
        );
    }

    return Invoice::make()
        ->buyer($customer)
        ->currencySymbol('KES')
        ->addItems($items)
        ->stream();

});

Route::post('/callback', [PaymentsController::class, 'callback']);
