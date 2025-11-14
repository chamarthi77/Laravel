<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Service Account Configuration
    |--------------------------------------------------------------------------
    |
    | These values are used by your Firebase Auth middleware or helper service
    | to verify ID tokens. The private key is stored inline in your .env file.
    |
    */

    'project_id' => env('FIREBASE_PROJECT_ID'),

    'client_email' => env('FIREBASE_CLIENT_EMAIL'),

    // Replace escaped "\n" with real newlines at runtime
    'private_key' => str_replace('\n', "\n", env('FIREBASE_PRIVATE_KEY')),
];
