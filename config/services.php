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

    'sonicpesa' => [
        'base_url' => env('SONIC_PESA_BASE_URL', 'https://api.sonicpesa.com/api/v1'),
        'api_key' => env('SONIC_PESA_API_KEY'),
        'api_secret' => env('SONIC_PESA_API_SECRET'),
        'currency' => env('SONIC_PESA_CURRENCY', 'TZS'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
    ],

    'sticker' => [
        // auto = Gemini when key present, else Tesseract. Or force "gemini"/"tesseract".
        'driver' => env('STICKER_OCR_DRIVER', 'auto'),
        'tesseract_binary' => env('TESSERACT_BINARY', 'tesseract'),
    ],

];
