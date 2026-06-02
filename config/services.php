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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'turnstile' => [
        // Set TURNSTILE_USE_TEST_KEYS=true in .env for local dev (works on localhost / 127.0.0.1)
        'use_test_keys' => (bool) env('TURNSTILE_USE_TEST_KEYS', false),
        // Skip HTTP verify entirely (local only recommended)
        'skip_verify' => (bool) env('TURNSTILE_SKIP_VERIFY', false),
        'site_key' => env('TURNSTILE_USE_TEST_KEYS', false)
            ? '1x00000000000000000000AA'
            : env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_USE_TEST_KEYS', false)
            ? '1x0000000000000000000000000000000AA'
            : env('TURNSTILE_SECRET_KEY'),
    ],

];
