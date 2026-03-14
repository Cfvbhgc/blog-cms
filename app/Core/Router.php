<?php

namespace App\Core;

/**
 * Simple URL router with support for named parameters.
 */
class Router
{
    private array $routes = [];

    /**
     * Register a GET route.
     */
    public function get(string $pattern, string $controller, string $action): void
    {
        $this->addRoute('GET', $pattern, $controller, $action);
    }

    /**
     * Register a POST route.
     */
    public function post(string $pattern, string $controller, string $action): void
    {
        $this->addRoute('POST', $pattern, $controller, $action);
    }

    private function addRoute(string $method, string $pattern, string $controller, string $action): void
    {
        // Convert route pattern to regex: {param} -> (?P<param>[^/]+)
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        $this->routes[] = [
            'method'     => $method,
            'pattern'    => $regex,
            'controller' => $controller,
            'action'     => $action,
        ];
    }

    /**
     * Dispatch the current request to the matching controller/action.
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                $controllerClass = "App\\Controllers\\" . $route['controller'];
                $action = $route['action'];

                if (!class_exists($controllerClass)) {
                    $this->sendError(500, "Controller {$controllerClass} not found");
                    return;
                }

                $controller = new $controllerClass();

                if (!method_exists($controller, $action)) {
                    $this->sendError(500, "Action {$action} not found in {$controllerClass}");
                    return;
                }

                call_user_func_array([$controller, $action], $params);
                return;
            }
        }

        // No route matched
        $this->sendError(404, 'Page not found');
    }

    private function sendError(int $code, string $message): void
    {
        http_response_code($code);
        $controller = new \App\Controllers\ErrorController();
        $controller->show($code, $message);
    }
}
