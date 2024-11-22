<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable hosting queue worker
    |--------------------------------------------------------------------------
    |
    | Set to true if this instance is running on hosting. Activation
    | will start the command "queue:work" through the "Screen". The instance
    | must have a scheduler configured every minute.
    |
    */

    'queue_enabled' => env('LARAVEL_HOSTING_QUEUE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Path to the php.ini file for the CLI
    |--------------------------------------------------------------------------
    |
    | For example, at SeoHost hosting, for php commands, additional
    | configuration is required in the php.ini file. Useful in case
    | of proc_open error.
    |
    */

    'php_ini_path' => env('LARAVEL_HOSTING_PHP_INI_PATH'),
];
