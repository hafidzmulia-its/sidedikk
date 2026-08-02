<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$laravelBasePath = dirname(__DIR__, 2).'/sidedikk';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $laravelBasePath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $laravelBasePath.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $laravelBasePath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
