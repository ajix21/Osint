<?php

return [
    /*
    |--------------------------------------------------------------------------
    | LeakOSINT Global API Token
    |--------------------------------------------------------------------------
    | Token ini digunakan oleh semua user yang tidak memiliki token pribadi.
    | Set di .env: LEAKOSINT_API_TOKEN=xxxx:xxxx
    */
    'api_token' => env('LEAKOSINT_API_TOKEN', ''),
];
