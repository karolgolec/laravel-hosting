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
];
