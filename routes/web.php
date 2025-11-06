<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;

Route::get('/', function () {
    return view('welcome');
});

// Health check route
Route::get('/alive', [HealthController::class, 'alive']);
