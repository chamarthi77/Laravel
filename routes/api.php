<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\IncidentController;


Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/incidents', [IncidentController::class, 'index']);


// ---------------------------------------------------------
// Health check
// ---------------------------------------------------------
Route::get('/alive', [HealthController::class, 'alive']);

// ---------------------------------------------------------
// 👇 Public route for initial Firebase registration
// ---------------------------------------------------------
Route::post('/admin/users', [UserController::class, 'store']); 
// (no auth — called right after Firebase signup)

// ---------------------------------------------------------
// Admin routes (protected by Firebase + RBAC)
// ---------------------------------------------------------
Route::prefix('admin')
    ->middleware(['firebase.auth'])
    ->group(function () {

        // ----- Users -----
        Route::get('/users', [UserController::class, 'index']);
        
        // ✅ Allow Firebase UID lookups & updates after login
        Route::get('/users/{uid}', [UserController::class, 'showByFirebase']);
        Route::put('/users/{uid}', [UserController::class, 'updateByFirebase']);
        Route::delete('/users/{uid}', [UserController::class, 'destroyByFirebase']);

        // ----- Projects -----
        Route::get('/projects', [ProjectController::class, 'index']);
        Route::post('/projects', [ProjectController::class, 'store']);
        Route::get('/projects/{project}', [ProjectController::class, 'show']);
        Route::put('/projects/{project}', [ProjectController::class, 'update']);
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);

        // ----- Permissions -----
        Route::get('/permissions', [PermissionController::class, 'index']);
        Route::post('/permissions', [PermissionController::class, 'store']);
        Route::get('/permissions/{permission}', [PermissionController::class, 'show']);
        Route::put('/permissions/{permission}', [PermissionController::class, 'update']);
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy']);
    });

// ---------------------------------------------------------
// Optional: role-specific test routes (keep for debugging)
// ---------------------------------------------------------
Route::middleware(['firebase.auth', 'role:SUPER_ADMIN'])
    ->get('/admin-only', fn () => response()->json(['ok' => true]));

Route::middleware(['firebase.auth', 'role:COMMUNITY_ADMIN'])
    ->get('/community', fn () => response()->json(['ok' => true]));

Route::middleware(['firebase.auth', 'role:ORG_MANAGER'])
    ->get('/org', fn () => response()->json(['ok' => true]));
