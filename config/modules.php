<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Modules Path
    |--------------------------------------------------------------------------
    |
    | Path to the modules directory where all modules are stored.
    |
    */
    'path' => app_path('Modules'),

    /*
    |--------------------------------------------------------------------------
    | Modules Namespace
    |--------------------------------------------------------------------------
    |
    | Root namespace for all modules.
    |
    */
    'namespace' => 'App\\Modules',

    /*
    |--------------------------------------------------------------------------
    | Module Cache
    |--------------------------------------------------------------------------
    |
    | Enable or disable caching of module metadata for performance.
    |
    */
    'cache_enabled' => env('MODULE_CACHE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Auto Discover Modules
    |--------------------------------------------------------------------------
    |
    | Automatically discover modules in the modules directory.
    | When disabled, modules must be registered manually.
    |
    */
    'auto_discover' => env('MODULE_AUTO_DISCOVER', false),
];
