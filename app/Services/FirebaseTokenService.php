<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseTokenService
{
    protected $auth;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(base_path('firebaseServiceAccount.json'));
        $this->auth = $factory->createAuth();
    }

    public function verify(string $idToken): array
    {
        $verifiedIdToken = $this->auth->verifyIdToken($idToken);
        return $verifiedIdToken->claims()->all();
    }
}
