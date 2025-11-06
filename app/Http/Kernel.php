<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{

   
protected $routeMiddleware = [
   

    // ✅ Firebase middleware alias
    'firebase.auth' => \App\Http\Middleware\VerifyFirebaseToken::class,

    // Other custom middleware
    'role'            => \App\Http\Middleware\EnsureRole::class,
    'super.admin'     => \App\Http\Middleware\EnsureSuperAdmin::class,
];



}

