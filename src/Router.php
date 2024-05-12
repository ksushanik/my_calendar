<?php

// src/Router.php
class Router
{
    private $getRoutes = [];
    private $postRoutes = [];
    private $deleteRoutes = [];

    public function get($route, $action)
    {
        // Преобразовываем параметры в регулярные выражения
        $route = preg_replace('/\{([a-z]+)\}/', '([^/]+)', $route);
        $this->getRoutes[$route] = $action;
    }

    public function post($route, $action)
    {
        $this->postRoutes[$route] = $action;
    }

    public function delete($route, $action)
    {
        $this->deleteRoutes[$route] = $action;
    }

    public function dispatch()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if ($requestMethod == 'GET') {
            $action = $this->matchRoute($requestUri, $this->getRoutes);
        } else if ($requestMethod == 'POST') {
            $action = $this->matchRoute($requestUri, $this->postRoutes);
        } else if ($requestMethod === 'DELETE') {
            $action = $this->matchRoute($requestUri, $this->deleteRoutes);
        } else {
            $action = null;
        }

        if (is_callable($action)) {
            call_user_func($action);
        } else {
            // 404 Not Found
            header("HTTP/1.0 404 Not Found");
            echo "404 Not Found";
        }
    }

    private function matchRoute($requestUri, $routes)
    {
        foreach ($routes as $route => $action) {
            if (preg_match("/^" . str_replace("/", "\/", $route) . "$/", $requestUri, $matches)) {
                // Вытаскиваем все аргументы URL
                if ($matches) {
                    $args = array_slice($matches, 1);
                    return function () use ($action, $args) {
                        call_user_func_array($action, $args);
                    };
                }
                return $action;
            }
        }
        return null;
    }
}
