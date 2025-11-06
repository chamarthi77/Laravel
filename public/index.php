<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Maintenance mode
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Composer autoload
require __DIR__.'/../vendor/autoload.php';

// Bootstrap the app
$app = require_once __DIR__.'/../bootstrap/app.php';

/** @var \Illuminate\Foundation\Application $app */
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Capture request and send through kernel
$request = Request::capture();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
