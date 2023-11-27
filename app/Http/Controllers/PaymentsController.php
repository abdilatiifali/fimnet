<?php

namespace App\Http\Controllers;

use App\Enums\PaymentType;
use App\Models\Customer;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Providers\CustomerSubscriptionUpdated;
use Http;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{

    public function token()
    {
        $response = Http::withBasicAuth(
            config('services.mpesa.key'),
            config('services.mpesa.secret'),
        )
        ->get(config('services.mpesa.tokenUrl'))
        ->json(['access_token']);

        return $response;
    }

    public function registerUrl()
    {
        return Http::withToken($this->token())
            ->post(config('services.mpesa.registerUrl'), [
                'ValidationURL' => env('APP_URL').'/validation',
                'ConfirmationURL' => env('APP_URL').'/confirmation',
                'ResponseType' => 'completed',
                'ShortCode' => config('services.mpesa.shortCode'),
            ])->json();
    }

    public function validation()
    {
        $accountNumber = preg_replace('/\s+/', '', request('BillRefNumber'));
        $customer = Customer::where('mpesaId', $accountNumber)->first();

        if (! $customer) {
            return response()->json([
                'ResultCode' => 'C2B00012',
                'ResultDesc' => 'Rejected',
            ]);
        }

        if (request('BusinessShortCode') !== config('services.mpesa.shortCode')) {
            return response()->json([
                'ResultCode' => 'C2B00015',
                'ResultDesc' => 'Rejected',
            ]);
        }

        if (request('TransAmount') < $customer->amount) {
            return response()->json([
                'ResultCode' => 'C2B00013',
                'ResultDesc' => 'Rejected',
            ]);
        }

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    public function confirmation()
    {
        $accountNumber = preg_replace('/\s+/', '', request('BillRefNumber'));

        $customer = Customer::where('mpesaId', $accountNumber)->firstOrFail();

        $pivot = Subscription::where('customer_id', $customer->id)
                ->where('month_id', now()->month)
                ->where('session_id', config('app.year'))
                ->first();

        $pivot
            ? $this->updateSubscription($customer, $pivot, request('TransAmount')) 
            : $pivot = $this->createSubscription($customer, request('TransAmount'));


        event(new CustomerSubscriptionUpdated($pivot));

        return 'done';
    }

    public function updateSubscription($customer, $pivot, $transAmount)
    {
        $pivot = $pivot->update([
            'amount_paid' => intval($transAmount) + $pivot->amount_paid,
            'payment_type' => PaymentType::mpesa->value,
            'balance' => $pivot->amount - (intval($transAmount) + $pivot->amount_paid),
            'paid' => true,
        ]);

        $this->updateCustomerBlockDay($customer);

        return $pivot;

    }

    protected function updateCustomerBlockDay($customer) 
    {
        if (optional($customer->house)->block_day !== now()->day) {
            $customer->update(['block_day' => now()->day]);
        }

        return;
    }

    public function createSubscription($customer, $transAmount)
    {
        $subscription = Subscription::create([
            'customer_id' => $customer->id,
            'month_id' => now()->month,
            'session_id' => config('app.year'),
            'amount' => $customer->amount,
            'amount_paid' => intval($transAmount),
            'payment_type' => PaymentType::mpesa->value,
            'balance' => $customer->amount - intval($transAmount),
            'paid' => true,
        ]);

        $this->updateCustomerBlockDay($customer);

        return $subscription;
    }

    public function stkdpush(Request $request)
    {        
        $customer = Customer::findOrFail($request->customerId);

        $passKey = config('services.mpesa.passKey');
        $timestap = date('YmdHis');
        $code = config('services.mpesa.shortCode');

        $response = Http::withToken($this->token())
            ->post(config('services.mpesa.stdkUrl'), [
                "BusinessShortCode" => $code,
                "Password" => base64_encode($code . $passKey . $timestap), 
                "Timestamp" => $timestap,
                "TransactionType" => "CustomerPayBillOnline",    
                "Amount" => $customer->amount,
                "PartyA" => $customer->phone_number,
                "PartyB" => $code,
                "PhoneNumber" => $customer->phone_number,
                "CallBackURL" => config('app.url') . '/callback',   
                "AccountReference" => "Test",    
                "TransactionDesc" => "Test"
            ]);

        return response()->json([
            'message' => 'successfully pushed',
        ], 200);
    }

    public function callback(Request $request)
    {
        \Log::info('Recieved');
        \Log::info(request('Body'));
        $amount = request('Body')['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
        $phoneNumber = request('Body')['stkCallback']['CallbackMetadata']['Item'][4]['Value'];
        \Log::info($amount);
        \Log::info($phoneNumber);

        $customer = Customer::where('phone_number', $phoneNumber)->firstOrFail();

        $pivot = Subscription::where('customer_id', $customer->id)
                ->where('month_id', now()->month)
                ->where('session_id', config('app.year'))
                ->first();

        $pivot
            ? $this->updateSubscription($pivot, $amount) 
            : $pivot = $this->createSubscription($customer, $amount);

        event(new CustomerSubscriptionUpdated($pivot));

        \Log::info('done');
    }
}
