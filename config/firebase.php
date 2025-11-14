<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Firebase Credentials
    |--------------------------------------------------------------------------
    | These are used by the Kreait Firebase SDK for authentication and database access.
    */
    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS', base_path('firebaseServiceAccount.json')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Project-specific Config (for custom middlewares)
    |--------------------------------------------------------------------------
    | The VerifyFirebaseToken middleware expects:
    | config('firebase.projects.app.credentials')
    |
    | So we alias the same credentials here.
    */
    'projects' => [
        'app' => [
            'credentials' => env('FIREBASE_CREDENTIALS', base_path('firebaseServiceAccount.json')),
        ],
    ],

];
