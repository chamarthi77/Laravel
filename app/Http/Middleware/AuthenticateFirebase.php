<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\FirebaseTokenService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticateFirebase
{
    protected $firebaseTokenService;

    public function __construct(FirebaseTokenService $firebaseTokenService)
    {
        $this->firebaseTokenService = $firebaseTokenService;
    }

    public function handle(Request $request, Closure $next)
    {
        \Log::debug('AuthenticateFirebase middleware is running');

        $bearer = $request->bearerToken();
        if (!$bearer) {
            Log::error("AuthenticateFirebase: Missing token");
            return response()->json(['ok' => false, 'error' => 'missing_token'], 401);
        }

        try {
            Log::info("AuthenticateFirebase: Verifying token...");
            $claims = $this->firebaseTokenService->verify($bearer);

            $uid           = $claims['user_id'] ?? $claims['sub'] ?? null;
            $email         = $claims['email']   ?? null;
            $emailVerified = (bool)($claims['email_verified'] ?? false);
            $displayName   = $claims['name']    ?? null;

            Log::info("AuthenticateFirebase: Claims decoded", $claims);

            if (!$uid || !$email) {
                return response()->json(['ok' => false, 'error' => 'invalid_claims'], 401);
            }
            if (!$emailVerified) {
                return response()->json(['ok' => false, 'error' => 'email_not_verified'], 403);
            }
$user = User::firstOrCreate(
    ['firebase_uid' => $uid],
    [
        'email' => $email,
        'display_name' => $displayName,
        'email_verified' => $emailVerified,
        'permission_id' => \DB::table('permissions')->where('key', 'USER')->value('id') // assign USER role
    ]
);


            $request->attributes->set('auth_user', $user);
            $request->attributes->set('firebase_uid', $uid);
            $request->attributes->set('firebase_email', $email);

            Auth::setUser($user);

            return $next($request);

        } catch (\Throwable $e) {
            Log::error("AuthenticateFirebase: Exception - " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['ok' => false, 'error' => 'invalid_token'], 401);
        }
    }
}
