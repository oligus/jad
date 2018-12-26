<?php declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Path prefix
    |--------------------------------------------------------------------------
    |
    | This option controls path prefix that will be used when getting a JAD
    | resource.
    |
    */
    'pathPrefix' => env('JAD_PATH_PREFIX', '/api/jad'),

    /*
    |--------------------------------------------------------------------------
    | Debug
    |--------------------------------------------------------------------------
    |
    | This option pretty prints json output
    |
    */
    'debug' => env('JAD_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | CORS
    |--------------------------------------------------------------------------
    |
    | This option sets Access-Control-Allow-Origin to * (disables cors)
    |
    */
    'cors' => env('JAD_CORS', false),

    /*
    |--------------------------------------------------------------------------
    | MAX PAGE SIZE
    |--------------------------------------------------------------------------
    |
    | This option sets maximum number of records, defaults to 25, can be
    | overrided with page[size]=25
    |
    */
    'max_page_size' => env('JAD_MAX_PAGE_SIZE', 25),

    /*
    |--------------------------------------------------------------------------
    | STRICT
    |--------------------------------------------------------------------------
    |
    | This option sets strict mode:
    | Throw not found error on missing resource (other wise returns an empty string)
    |
    */
    'strict' => env('JAD_STRICT', false),
];
