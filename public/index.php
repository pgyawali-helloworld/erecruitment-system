<?php
/**
 * Application Entry Point
 * Starts session, registers PSR-4 autoloader, loads configuration,
 * and dispatches request to the router.
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configurations
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

// PSR-4 Autoloader Implementation
spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'App\\';

    // Base directory for the namespace prefix
   $base_dir = __DIR__ . '/../app/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load routes & get router instance
$router = require_once __DIR__ . '/../app/config/routes.php';

// Dispatch request URL and method
$requestUrl = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

$router->dispatch($requestUrl, $requestMethod);
