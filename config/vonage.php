<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Vonage API Credentials
    |--------------------------------------------------------------------------
    |
    | Your Vonage API Key and Secret can be found in your Vonage Dashboard
    | at https://dashboard.nexmo.com/
    |
    */

    'api_key' => env('VONAGE_API_KEY'),
    'api_secret' => env('VONAGE_API_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | From Number
    |--------------------------------------------------------------------------
    |
    | The phone number or sender ID that will appear as the sender of your SMS.
    | This can be a Vonage virtual number or an alphanumeric sender ID.
    | Example: '+15551234567' or 'VIJO'
    |
    */

    'from' => env('VONAGE_FROM_NUMBER', 'VIJO'),

    /*
    |--------------------------------------------------------------------------
    | SMS Settings
    |--------------------------------------------------------------------------
    |
    | Additional SMS configuration options
    |
    */

    'sms' => [
        'default_type' => 'text', // text or unicode
        'concat_enabled' => true, // Enable message concatenation for long messages
    ],

];
