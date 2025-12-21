<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Route middleware aliases (Laravel 12)
     */
    protected $middlewareAliases = [
        'firebase.auth' => \App\Http\Middleware\AuthenticateFirebase::class,
        'role'          => \App\Http\Middleware\RoleMiddleware::class,
    ];
}
