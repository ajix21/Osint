<?php

return [
    /*
    |--------------------------------------------------------------------------
    | LeakOSINT API
    |--------------------------------------------------------------------------
    */
    'api_url' => env('LEAKOSINT_API_URL', 'https://leakosintapi.com/'),

    'api_token' => env('LEAKOSINT_API_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Brute-Force Protection
    |--------------------------------------------------------------------------
    */
    'max_attempts'    => (int) env('AUTH_MAX_ATTEMPTS', 5),
    'lockout_minutes' => (int) env('AUTH_LOCKOUT_MINUTES', 15),
];
