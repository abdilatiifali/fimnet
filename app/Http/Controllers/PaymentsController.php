<?php

namespace App\Http\Controllers;

use App\Enums\PaymentType;
use App\Models\Customer;
use App\Models\Income;
use App\Models\Month;
use App\Models\Subscription;
use App\Providers\CustomerSubscriptionUpdated;
use Http;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        $customer = Customer::where('mpesaId', request('BillRefNumber'))->first();

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

    public function excessAmount($customer, $amount)
    {
        return $amount > $customer->amount ? $amount - $customer->amount : 0;
    }

    public function confirmation()
    {
        \DB::transaction(function () { 
            $customer = Customer::where('mpesaId', request('BillRefNumber'))->firstOrFail();

            $pivot = Subscription::where('customer_id', $customer->id)
                ->where('month_id', now()->month)
                ->where('session_id', config('app.year'))
                ->first();

            Income::create([
                'code' => request('TransID'),
                'transaction_time' => Carbon::parse(request('TransTime'))->format('d-m-Y H:ia'),
                'paid_by' => request('FirstName'),
                'customer_id' => $customer->id,
                'month_id' => Month::where('id', now()->month)->first()->id,
                'amount_paid' => request('TransAmount'),
                'excess_amount' => $this->excessAmount($customer, request('TransAmount')),
                'balance' => $customer->amount - request('TransAmount'),
                'phone_number' => request('MSISDN'),
                'account_number' => request('BillRefNumber'),
                'router_id' => $customer->router->id,
                'house_id' => $customer->house->id,
            ]);

            $pivot
                ? $this->updateSubscription($customer, $pivot, request('TransAmount'))
                : $pivot = $this->createSubscription($customer, request('TransAmount'));

            event(new CustomerSubscriptionUpdated($pivot));
        });
       

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

        return $pivot;

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
                'BusinessShortCode' => $code,
                'Password' => base64_encode($code.$passKey.$timestap),
                'Timestamp' => $timestap,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $customer->balance(),
                'PartyA' => $customer->phone_number,
                'PartyB' => $code,
                'PhoneNumber' => $customer->phone_number,
                'CallBackURL' => config('app.url').'/callback',
                'AccountReference' => 'Test',
                'TransactionDesc' => 'Test',
            ]);

        return response()->json([
            'message' => 'successfully pushed',
        ], 200);
    }

    public function callback(Request $request)
    {
        \Log::info(request('Body'));
        if (request('Body')['stkCallback']['ResultCode'] != 0) {
            \Log::info('cancelled');

            return;
        }

        $amount = request('Body')['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
        $phoneNumber = request('Body')['stkCallback']['CallbackMetadata']['Item'][3]['Value'];

        \Log::info([$amount, $phoneNumber]);

        $customer = Customer::where('phone_number', $phoneNumber)->first();

        if (! $customer) {
            \Log::inf('there is no a customer available');

            return;
        }

        $pivot = Subscription::where('customer_id', $customer->id)
            ->where('month_id', now()->month)
            ->where('session_id', config('app.year'))
            ->first();

        $pivot
            ? $this->updateSubscription($customer, $pivot, $amount)
            : $pivot = $this->createSubscription($customer, $amount);

        event(new CustomerSubscriptionUpdated($pivot));

        \Log::info('done');

        return 'done';
    }
}
