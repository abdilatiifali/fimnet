<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PdfController;
use App\Models\Customer;
use App\Models\Month;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;
use App\Enums\PaymentType;

Route::post("/login", [AuthController::class, 'store']);
Route::post("/logout", [AuthController::class, 'destory']);

Route::get("/statement/{customer}", [PdfController::class, 'index']);

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

Route::post("/callback", function () {
    $amount = request('Body')['stkCallback']['CallbackMetadata']['Item'][0]['Value'];

    $pivot = Subscription::where('customer_id', 2)
                ->where('month_id', now()->month)
                ->where('session_id', config('app.year'))
                ->first();

    $pivot->update([
        'amount_paid' => intval($amount) + $pivot->amount_paid,
        'payment_type' => PaymentType::mpesa->value,
        'balance' => $pivot->amount - (intval($amount) + $pivot->amount_paid),
        'paid' => true,
    ]);

    return 'done';
});
