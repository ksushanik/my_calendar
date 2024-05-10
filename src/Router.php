<?php
// src/Router.php
class Router
{
    private $getRoutes = [];
    private $postRoutes = [];

    public function get($route, $action)
    {
        $this->getRoutes[$route] = $action;
    }

    public function post($route, $action)
    {
        $this->postRoutes[$route] = $action;
    }

    public function dispatch()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if ($requestMethod == 'GET' && isset($this->getRoutes[$requestUri])) {
            call_user_func($this->getRoutes[$requestUri]);
        } else if ($requestMethod == 'POST' && isset($this->postRoutes[$requestUri])) {
            call_user_func($this->postRoutes[$requestUri]);
        } else {
            // 404 Not Found
            header("HTTP/1.0 404 Not Found");
            echo "404 Not Found";
        }
    }
}
