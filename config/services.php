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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'otp' => [
        'provider' => env('OTP_PROVIDER', 'log'),
        'endpoint' => env('OTP_SMS_ENDPOINT'),
        'api_key' => env('OTP_SMS_API_KEY'),
        'sender' => env('OTP_SMS_SENDER', 'NABHA'),
        'message_template' => env('OTP_MESSAGE_TEMPLATE', 'Your OTP is :otp. It will expire in 5 minutes.'),
        'timeout' => env('OTP_SMS_TIMEOUT', 8),
    ],

];
