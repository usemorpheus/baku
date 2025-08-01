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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'twitter' => [
        'api_key'             => env('TWITTER_CONSUMER_KEY'),
        'api_secret'          => env('TWITTER_CONSUMER_SECRET'),
        'access_token'        => env('TWITTER_ACCESS_TOKEN'),
        'access_token_secret' => env('TWITTER_ACCESS_TOKEN_SECRET'),
        'bearer_token'        => env('TWITTER_BEARER_TOKEN'),
        'api_version'         => env('TWITTER_API_VERSION', 2),
        'api_url'             => env('TWITTER_API_URL', 'https://api.twitter.com'),
        'client_id'           => env('TWITTER_CLIENT_ID'),
        'client_secret'       => env('TWITTER_CLIENT_SECRET'),
    ],

];
