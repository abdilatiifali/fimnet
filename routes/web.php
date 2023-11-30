<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\QuotationController;
use App\Models\Customer;
use App\Models\Month;
use App\Models\Subscription;
use Illuminate\Support\Facades\Route;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

Route::post('/login', [AuthController::class, 'store']);
Route::post('/logout', [AuthController::class, 'destory']);
Route::get('/quotations/{id}', [QuotationController::class, 'show']);

Route::get('/statement/{customer}', [PdfController::class, 'index']);

Route::post('/validation', [PaymentsController::class, 'validation']);
Route::get('/registerUrl', [PaymentsController::class, 'registerUrl']);
Route::post('/confirmation', [PaymentsController::class, 'confirmation']);

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

Route::post('/abdi', [PaymentsController::class, 'stkdpush']);

Route::post('/callback', [PaymentsController::class, 'callback']);
