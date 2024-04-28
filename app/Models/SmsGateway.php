<?php

namespace App\Models;

class SmsGateway
{
    public static function routerIsDown($key)
    {
        $message = "The ${key} is Down";
        $phoneNumber = '0745793438';

        static::sendSmsApi($phoneNumber, $message);
    }

    public static function routerIsUp($key)
    {
        $message = "The ${key} is Up again";
        $phoneNumber = '0745793438';

        static::sendSmsApi($phoneNumber, $message);
    }

    public static function sendSmsApi($phoneNumber, $message)
    {
        $response = \Http::asForm()->post('https://sms.imarabiz.com/api/services/sendsms/', [
            'partnerID' => config('services.sms.partnerId'),
            'apikey' => config('services.sms.apiKey'),
            'mobile' => $phoneNumber,
            'message' => $message,
            'shortcode' => config('services.sms.shortCode'),
            'pass_type' => 'plain', //bm5 {base64 encode} or plain
        ])->json();

        print_r($response);
    }

    public static function solveComplain($customer)
    {
        $message = "Hello {$customer->name}. We've resolved the issue you reported, and your ticket is now closed. Thank you for trusting Fimnet.";

        static::sendSmsApi($customer->phone_number, $message);
    }

    public static function sendComplain($customer)
    {
        $message = "Hello {$customer->name}. Thank you for reaching out. We've received your complaint and are actively working on resolving it. Rest assured, we'll address the issue as soon as possible. Thank you for your patience.";

        static::sendSmsApi($customer->phone_number, $message);
    }

    public static function sendSms($customer)
    {
        $paybill = $customer->house->district->paybill_number;

        $message = "Hello {$customer->name}, this is a reminder from FIMNET COMMUNICATION that your payment is now due for our services. Please Pay as soon as possible. Our paybill number is ${paybill}  and your account number is {$customer->mpesaId}. You can call us from 0745683323 or 0799903635 Thanks.";

        static::sendSmsApi($customer->phone_number, $message);

    }

    public static function sendStaffMessage($houseIds)
    {
        $houseNames = implode(',', House::whereIn('id', $houseIds)->pluck('name')->toArray());

        $message = "Dear Team, It has come to our attention that the current disconnection of internet services in ${houseNames} is due to unresolved payment issues.";

        $staffs = [

        ];

        foreach ($staffs as $staff) {
            static::sendSmsApi($staff, $message);
        }

        return 'done';
    }

    public static function sendInternetIsDownMessage($customer)
    {
        $message = 'We regret to inform you that there is currently an interruption in our internet services that may be affecting your connectivity. We apologize for any inconvenience this may cause and want to assure you that our team is working diligently to resolve the issue as soon as possible';

        static::sendSmsApi($customer->phone_number, $message);

    }

    public static function sendConfirmation($customer)
    {
        $message = "Hi {$customer->name}, Payment received! Your loyalty is greatly appreciated. Thank you.";

        static::sendSmsApi($customer->phone_number, $message);
    }
}
