<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Kreait\Firebase\Factory;
use App\Models\User;

class VerifyFirebaseToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization', '');
        if (!preg_match('/Bearer\s+(.+)/i', $authHeader, $m)) {
            return response()->json(['message' => 'Missing Bearer token'], 401);
        }

        $idToken = trim($m[1]);

        try {
            $auth = (new Factory)
                ->withServiceAccount(config('firebase.projects.app.credentials'))
                ->createAuth();

            $verified = $auth->verifyIdToken($idToken);

            $firebaseUid   = $verified->claims()->get('sub');
            $firebaseEmail = $verified->claims()->get('email') ?? null;
            $firebaseName  = $verified->claims()->get('name')
                ?? ($firebaseEmail ? explode('@', $firebaseEmail)[0] : 'Unknown');

            /**
             * ✅ STEP 1: Log what comes in
             */
            \Log::info("🔥 VerifyFirebaseToken called", [
                'uid'   => $firebaseUid,
                'email' => $firebaseEmail,
                'name'  => $firebaseName,
            ]);

            /**
             * ✅ STEP 2: Insert or update user in MySQL
             */
            $user = User::updateOrCreate(
                ['firebase_uid' => $firebaseUid],
                [
                    'email' => $firebaseEmail,
                    'name'  => $firebaseName,
                    'role'  => User::where('firebase_uid', $firebaseUid)->exists()
                        ? User::where('firebase_uid', $firebaseUid)->value('role')
                        : 'USER',
                ]
            );

            // Attach user for later use
            $request->setUserResolver(fn () => $user);

        } catch (\Throwable $e) {
            \Log::error("❌ Firebase verification failed", ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Invalid token',
                'error'   => $e->getMessage(),
            ], 401);
        }

        return $next($request);
    }
}
