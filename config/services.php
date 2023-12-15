<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'sms' => [
        'apiKey' => env('SMS_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'mpesa' => [
        'key' => env('MPESA_KEY'),
        'secret' => env('MPESA_SECRET'),
        'shortCode' => env('MPESA_SHORTCODE'),
        'passKey' => env('MPESA_PASSKEY'),
        'tokenUrl' => env('MPESA_TOKEN_URL'),
        'stdkUrl' => env('MPESA_STDKURL'),
        'registerUrl' => env('MPESA_REGISTER_URL'),
    ],

    'fimnet2' => [
        'key' => env('FIMNET_TWO_KEY'),
        'secret' => env('FIMNET_TWO_SECRET'),
        'shortCode' => env('FIMNET_TWO_SHORTCODE'),
        'passKey' => env('FIMNET2_MPESA_PASSKEY'),
        'tokenUrl' => env('MPESA_TOKEN_URL'),
        'stdkUrl' => env('MPESA_STDKURL'),
        'registerUrl' => env('MPESA_REGISTER_URL'),
    ],

     'fimnet3' => [
        'key' => env('FIMNET_THREE_KEY'),
        'secret' => env('FIMNET_THREE_SECRET'),
        'shortCode' => env('FIMNET_THREE_SHORTCODE'),
        'passKey' => env('FIMNET2_MPESA_PASSKEY'),
        'tokenUrl' => env('MPESA_TOKEN_URL'),
        'stdkUrl' => env('MPESA_STDKURL'),
        'registerUrl' => env('MPESA_REGISTER_URL'),
    ],

    'sms' => [
        'shortCode' => env('SMS_SHORTCODE'),
        'apiKey' => env('SMS_API_KEY'),
        'partnerId' => env('PARTNER_ID'),
    ],

    'mikrotik' => [
        'user' => env('MIKROTIK_USER'),
        'password' => env('MIKROTIK_PASSWORD'),
    ],

];
