<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Check for .env (Create Minimal Version if Missing)
|--------------------------------------------------------------------------
|
| If .env doesn't exist, create a minimal version to allow Laravel to boot
| This prevents 500 errors when accessing /install route
|
*/

if (!file_exists(__DIR__ . '/../.env')) {
    // Generate a random APP_KEY
    $appKey = 'base64:' . base64_encode(random_bytes(32));

    // Create minimal .env to allow Laravel to boot
    $minimalEnv = "APP_NAME=Laravel
APP_ENV=local
APP_KEY={$appKey}
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

CACHE_DRIVER=file
SESSION_DRIVER=file
";

    file_put_contents(__DIR__ . '/../.env', $minimalEnv);
    chmod(__DIR__ . '/../.env', 0666);

    // Check if we're not already on the install route
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($requestUri, '/install') === false) {
        // Get the script name to determine the base path
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = str_replace('/index.php', '', $scriptName);
        $basePath = str_replace('/public', '', $basePath);

        // Redirect to install route (relative to current path)
        header('Location: ' . $basePath . '/public/install');
        exit;
    }
}

// Manual Plugin Autoloader (Fixes "Class not found" without dump-autoload)
spl_autoload_register(function ($class) {
    if (str_starts_with($class, 'Plugins\\')) {
        $relativeClass = substr($class, strlen('Plugins\\'));
        $path = __DIR__ . '/../resources/views/plugins/' . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($path)) {
            require $path;
        }
    }
});

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
