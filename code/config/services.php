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
                'domain' => 'mail.smartsponsored.com',
        'secret' => 'k=rsa; p=MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC2OdogT9zhfqXYZ/ToHLbC9jhWAjZRT40o0e7jKF8Q22hP4xbKjl3Ea6IKJXaanTG+HM/rpsTvAgiJUNARwMSPP8pTBzDojj+vjcdWr3wGXr5WSrvppBvgcpLoFiEebItzM92vm1zaBGeT0HUd3gO5+sAtNaY7pvbhfXHxzKqVCwIDAQAB'
        // 'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
