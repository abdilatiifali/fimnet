<?php

namespace App\Http\Controllers;

use App\Enums\PaymentType;
use App\Jobs\PaymentConfirmation;
use App\Models\Customer;
use App\Models\Income;
use App\Models\Month;
use App\Models\Subscription;
use App\Providers\CustomerSubscriptionUpdated;
use Carbon\Carbon;
use Http;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{

    protected $paybillNames = [
        1 => 'mpesa',
        2 => 'fimnet2',
        3 => 'fimnet3'
    ];

    protected $tokens = [
        1 => 'token',
        2 => 'fimnet2Token',
        3 => 'fimnetThreeToken',
    ];

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

    public function fimnet2Token()
    {
        $response = Http::withBasicAuth(
            config('services.fimnet2.key'),
            config('services.fimnet2.secret'),
        )
            ->get(config('services.fimnet2.tokenUrl'))
            ->json(['access_token']);

        return $response;
    }

    public function fimnetThreeToken()
    {
        $response = Http::withBasicAuth(
            config('services.fimnet3.key'),
            config('services.fimnet3.secret'),
        )
            ->get(config('services.fimnet3.tokenUrl'))
            ->json(['access_token']);

        return $response;
    }

    public function fimnetThreeRegisterUrl()
    {
        return Http::withToken($this->fimnetThreeToken())
            ->post(config('services.fimnet3.registerUrl'), [
                'ValidationURL' => env('APP_URL').'/fimnetThreeValidation',
                'ConfirmationURL' => env('APP_URL').'/fimnetThreeConfirmation',
                'ResponseType' => 'completed',
                'ShortCode' => config('services.fimnet3.shortCode'),
            ])->json();
    }

    public function fimnet2RegisterUrl()
    {
        return Http::withToken($this->fimnet2Token())
            ->post(config('services.fimnet2.registerUrl'), [
                'ValidationURL' => env('APP_URL').'/fimnet2Validation',
                'ConfirmationURL' => env('APP_URL').'/fimnet2Confirmation',
                'ResponseType' => 'completed',
                'ShortCode' => config('services.fimnet2.shortCode'),
            ])->json();
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

        if (request('BusinessShortCode') !== $customer->house->district->paybill_number) {
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

    public function formatDate($transTime)
    {
        $parsedDateTime = Carbon::createFromFormat('YmdHis', $transTime);

        $formattedDateTime = $parsedDateTime->format('d-M-Y H:i:s');

        return $formattedDateTime;
    }

    public function confirmation()
    {
        $accountNumber = preg_replace('/\s+/', '', request('BillRefNumber'));
        $customer = Customer::where('mpesaId', $accountNumber)->first();

        $formattedDate = $this->formatDate(request('TransTime'));

        $pivot = Subscription::where('customer_id', $customer->id)
            ->where('month_id', now()->month)
            ->where('session_id', config('app.year'))
            ->first();

        $pivot
            ? $this->updateSubscription($customer, $pivot, request('TransAmount'))
            : $pivot = $this->createSubscription($customer, request('TransAmount'));

        (new Income)->make(
            $customer, 
            request('TransID'), 
            $formattedDate, 
            request('FirstName'),
            request('TransAmount'),
            request('MSISDN'),
            request('BillRefNumber')
        );

        event(new CustomerSubscriptionUpdated($pivot));       

        PaymentConfirmation::dispatch($customer);
        
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
        $customer = auth()->user();
        $id = $customer->house->district->id;
        $area = $this->paybillNames[$id];
        $method = $this->tokens[$id];

        if (!$area) return;

        $phoneNumber =  preg_replace('/^.*?(?=7)/', '254', $customer->phone_number);
        $code = config("services.${area}.shortCode");
        $passKey = config("services.${area}.passKey");
        $timestap = date('YmdHis');
        
        $response = Http::withToken($this->$method())
            ->post(config('services.mpesa.stdkUrl'), [
                'BusinessShortCode' => $code,
                'Password' => base64_encode($code.$passKey.$timestap),
                'Timestamp' => $timestap,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $customer->balance(),
                'PartyA' => $phoneNumber,
                'PartyB' => $code,
                'PhoneNumber' => $phoneNumber,
                'CallBackURL' => "https://fimnetplus.net/callback",
                'AccountReference' => $customer->mpesaId,
                'TransactionDesc' => 'PAY MONTHLY INTERNEET FEE',
            ])->json();

        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'succesfully pushed',
            ]);
        }

        return redirect('/client');
    }

    public function callback(Request $request)
    {        
        if (request('Body')['stkCallback']['ResultCode'] == 1032) {
            return redirect('/client');
        }

        if (request('Body')['stkCallback']['ResultCode'] != 0) return;

        $amount = request('Body')['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
        $code = request('Body')['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
        $formattedDate = $this->formatDate(
            request('Body')['stkCallback']['CallbackMetadata']['Item'][3]['Value']
        );

        $phoneNumber = request('Body')['stkCallback']['CallbackMetadata']['Item'][4]['Value'];
        $phoneNumber = preg_replace('/^.*?(?=7)/', '0', $phoneNumber);

        $customer = Customer::where('phone_number',  $phoneNumber)->first();

        if (! $customer) {
            \Log::info('there is no a customer available');
            return;
        }

        $pivot = Subscription::where('customer_id', $customer->id)
            ->where('month_id', now()->month)
            ->where('session_id', config('app.year'))
            ->first();

        \Log::info($amount);
        \Log::info('pivot money');
        \Log::info($pivot->amount_paid);

        $pivot->update([
            'amount_paid' => $amount,
            'payment_type' => PaymentType::mpesa->value,
            'balance' => $pivot->amount - $amount,
            'paid' => true,
        ]);

        (new Income)->make(
            $customer, 
            $code,
            $formattedDate, 
            'stkPush',
            $amount,
            $phoneNumber,
            $customer->mpesaId
        );

        event(new CustomerSubscriptionUpdated($pivot));

        PaymentConfirmation::dispatch($customer);

        \Log::info('done');

        return 'done';
    }
}
