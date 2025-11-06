<?php
require __DIR__.'/vendor/autoload.php';

use Kreait\Firebase\Factory;

// Initialize Firebase Admin
$factory = (new Factory)->withServiceAccount(__DIR__.'/firebaseServiceAccount.json');
$auth = $factory->createAuth();

// User ID can be any unique string, e.g., database user ID or email
$uid = 'super-admin-user-id';

// Optional: add custom claims for roles
$customClaims = [
    'role' => 'super_admin'
];

try {
    $customToken = $auth->createCustomToken($uid, $customClaims);
    echo "Custom Token (use this in Authorization header):\n";
    echo $customToken->toString();
} catch (\Throwable $e) {
    echo "Error creating token: " . $e->getMessage();
}
