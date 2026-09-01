<?php
namespace App\Core;

/**
 * Router Class
 * Handles route registration, URL parsing, dynamic route parameter matching,
 * and dispatching requests to controllers and actions.
 */
class Router {
    protected $routes = [];

    /**
     * Add a GET route
     */
    public function get($route, $controllerAction) {
        $this->add('GET', $route, $controllerAction);
    }

    /**
     * Add a POST route
     */
    public function post($route, $controllerAction) {
        $this->add('POST', $route, $controllerAction);
    }

    /**
     * Register a route
     */
    public function add($method, $route, $controllerAction) {
        // Replace {param} with named capturing groups
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $route);
        // Build complete regex pattern matching from start to end of string
        $pattern = '#^' . $pattern . '$#';
        
        $this->routes[] = [
            'method' => strtoupper($method),
            'route' => $pattern,
            'controllerAction' => $controllerAction
        ];
    }

    /**
     * Dispatch the current request
     */
    public function dispatch($url, $method) {
        // Strip query string (e.g. /jobs?sort=desc -> /jobs)
        $url = parse_url($url, PHP_URL_PATH);
        
        // Remove trailing slash if present (except for root '/')
        if ($url !== '/' && substr($url, -1) === '/') {
            $url = rtrim($url, '/');
        }

        // Determine base path of the application (e.g., /erecruitment-system)
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        $scriptDir = str_replace('\\', '/', $scriptDir); // Normalize slashes for Windows
        if ($scriptDir !== '/' && $scriptDir !== '\\') {
            $basePath = rtrim($scriptDir, '/');
            if (strpos($url, $basePath) === 0) {
                $url = substr($url, strlen($basePath));
            }
        }

        if (empty($url)) {
            $url = '/';
        }

        foreach ($this->routes as $routeInfo) {
            if ($routeInfo['method'] === strtoupper($method) && preg_match($routeInfo['route'], $url, $matches)) {
                // Keep only string keys (named parameters from regex capturing groups)
                $params = array_filter($matches, function($key) {
                    return is_string($key);
                }, ARRAY_FILTER_USE_KEY);
                
                list($controllerName, $action) = explode('@', $routeInfo['controllerAction']);
                $controllerClass = "App\\Controllers\\" . $controllerName;

                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $action)) {
                        call_user_func_array([$controller, $action], $params);
                        return;
                    }
                }
                
                $this->sendNotFound();
                return;
            }
        }
        
        $this->sendNotFound();
    }

    /**
     * Send 404 Header and Load 404 View
     */
    protected function sendNotFound() {
        http_response_code(404);
        $errorView = APP_ROOT . '/views/errors/404.php';
        if (file_exists($errorView)) {
            require_once $errorView;
        } else {
            echo "<h1>404 - Page Not Found</h1>";
            echo "<p>The page you are looking for does not exist or has been moved.</p>";
        }
    }
}
