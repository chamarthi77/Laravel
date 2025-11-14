<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;
use Illuminate\Http\Request;

class FirebaseAuthMiddleware
{
    protected $auth;

    public function __construct()
    {
        // Load credentials from config/firebase_auth.php
        $serviceAccount = [
            "type" => "service_account",
            "project_id" => config('firebase_auth.project_id'),
            "private_key" => config('firebase_auth.private_key'),
            "client_email" => config('firebase_auth.client_email'),
        ];

        $factory = (new Factory)->withServiceAccount($serviceAccount);
        $this->auth = $factory->createAuth();
    }

    public function handle(Request $request, Closure $next)
    {
        try {
            $idToken = $this->parseToken($request);
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            $uid = $verifiedIdToken->claims()->get('sub');
            $request->merge(['firebase_uid' => $uid]);

        } catch (Exception $e) {
            return response()->json(['error' => 'Unauthorized: '.$e->getMessage()], 401);
        }

        return $next($request);
    }

    private function parseToken($request)
    {
        $header = $request->header('Authorization');
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            throw new Exception('Missing or invalid Authorization header');
        }
        return substr($header, 7);
    }
}
