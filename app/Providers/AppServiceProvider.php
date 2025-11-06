<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;        // ✅ import the correct Route facade
use App\Services\FirebaseTokenService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FirebaseTokenService::class, function ($app) {
            return new FirebaseTokenService();
        });
    }

    public function boot(): void
    {
        // ✅ now this works because Route is imported correctly
        Route::aliasMiddleware('firebase.auth', \App\Http\Middleware\AuthenticateFirebase::class);
    }
}
