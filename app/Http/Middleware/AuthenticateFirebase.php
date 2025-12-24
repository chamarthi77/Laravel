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
    protected FirebaseTokenService $firebaseTokenService;

    public function __construct(FirebaseTokenService $firebaseTokenService)
    {
        $this->firebaseTokenService = $firebaseTokenService;
    }

    public function handle(Request $request, Closure $next)

    {

    \Log::info('🔥 AUTH HEADER', [
    'authorization' => $request->header('Authorization'),
]);

        Log::debug('AuthenticateFirebase middleware is running');

        $bearer = $request->bearerToken();
        if (!$bearer) {
            return response()->json(['ok' => false, 'error' => 'missing_token'], 401);
        }

        try {
            $claims = $this->firebaseTokenService->verify($bearer);

            $uid           = $claims['user_id'] ?? $claims['sub'] ?? null;
            $email         = $claims['email']   ?? null;
            $emailVerified = (bool)($claims['email_verified'] ?? false);
            $name          = $claims['name']    ?? null;

            if (!$uid || !$email) {
                return response()->json(['ok' => false, 'error' => 'invalid_claims'], 401);
            }

            if (!$emailVerified) {
                return response()->json(['ok' => false, 'error' => 'email_not_verified'], 403);
            }

            // ✅ AUTH ONLY — NO ROLE MUTATION
            $user = User::where('firebase_uid', $uid)->first();

            if (!$user) {
                // Try matching by email
                $user = User::where('email', $email)->first();
            }

            if ($user) {
                $user->update([
                    'firebase_uid'   => $uid,
                    'email_verified' => $emailVerified,
                    'name'           => $name,
                ]);
            } else {
                $user = User::create([
                    'firebase_uid'   => $uid,
                    'email'          => $email,
                    'name'           => $name,
                    'email_verified' => $emailVerified,
                    'role'           => 'USER',
                ]);
            }

            $request->setUserResolver(fn () => $user);
            Auth::setUser($user);

            return $next($request);

        } catch (\Throwable $e) {
            Log::error('AuthenticateFirebase failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['ok' => false, 'error' => 'invalid_token'], 401);
        }
    }
}
