<?php

require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

$firebase = (new Factory)
    ->withServiceAccount(__DIR__.'/firebaseServiceAccount.json');

$auth = $firebase->createAuth();

// CHANGE THESE VARIABLES
$email = 'hansikagarapati.go@gmail.com';
$role = 'superadmin';

// Find the user by email
$user = $auth->getUserByEmail($email);

// Assign role via custom claim
$auth->setCustomUserClaims($user->uid, ['role' => $role]);

echo "Role '$role' assigned to $email successfully.\n";
