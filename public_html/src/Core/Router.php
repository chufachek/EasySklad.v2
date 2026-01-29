<?php

namespace Bramus\Router;

class Router
{
    private $basePath = '';
    private $routes = array();
    private $notFoundCallback;
    private $beforeRoutes = array();
    private $afterRoutes = array();
    private $namespace = '';

    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '/');
        if ($this->basePath === '/') {
            $this->basePath = '';
        }
        return $this;
    }

    public function get($pattern, $callback)
    {
        return $this->map('GET', $pattern, $callback);
    }

    public function post($pattern, $callback)
    {
        return $this->map('POST', $pattern, $callback);
    }

    public function put($pattern, $callback)
    {
        return $this->map('PUT', $pattern, $callback);
    }

    public function delete($pattern, $callback)
    {
        return $this->map('DELETE', $pattern, $callback);
    }

    public function patch($pattern, $callback)
    {
        return $this->map('PATCH', $pattern, $callback);
    }

    public function options($pattern, $callback)
    {
        return $this->map('OPTIONS', $pattern, $callback);
    }

    public function match($methods, $pattern, $callback)
    {
        return $this->map($methods, $pattern, $callback);
    }

    public function any($pattern, $callback)
    {
        return $this->map('GET|POST|PUT|DELETE|PATCH|OPTIONS', $pattern, $callback);
    }

    public function before($methods, $pattern, $callback)
    {
        $this->beforeRoutes[] = array($methods, $pattern, $callback);
        return $this;
    }

    public function after($methods, $pattern, $callback)
    {
        $this->afterRoutes[] = array($methods, $pattern, $callback);
        return $this;
    }

    public function mount($baseRoute, $callback)
    {
        $previousNamespace = $this->namespace;
        $this->namespace .= $baseRoute;
        call_user_func($callback);
        $this->namespace = $previousNamespace;
        return $this;
    }

    public function set404($callback)
    {
        $this->notFoundCallback = $callback;
        return $this;
    }

    public function run($callback = null)
    {
        $dispatched = false;
        $requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $requestUri = $this->getCurrentUri();

        if ($requestMethod === 'HEAD') {
            ob_start();
            $requestMethod = 'GET';
        }

        foreach ($this->beforeRoutes as $route) {
            list($methods, $pattern, $callback) = $route;
            if (!$this->matchMethod($methods, $requestMethod)) {
                continue;
            }
            if ($this->matchRoute($pattern, $requestUri, $params)) {
                call_user_func_array($callback, $params);
            }
        }

        foreach ($this->routes as $route) {
            list($methods, $pattern, $callback) = $route;
            if (!$this->matchMethod($methods, $requestMethod)) {
                continue;
            }

            if ($this->matchRoute($pattern, $requestUri, $params)) {
                $dispatched = true;
                call_user_func_array($callback, $params);
                break;
            }
        }

        foreach ($this->afterRoutes as $route) {
            list($methods, $pattern, $callback) = $route;
            if (!$this->matchMethod($methods, $requestMethod)) {
                continue;
            }
            if ($this->matchRoute($pattern, $requestUri, $params)) {
                call_user_func_array($callback, $params);
            }
        }

        if (!$dispatched) {
            if ($this->notFoundCallback) {
                call_user_func($this->notFoundCallback);
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
                echo '404 Not Found';
            }
        }

        if ($requestMethod === 'HEAD') {
            ob_end_clean();
        }

        if ($callback && is_callable($callback)) {
            call_user_func($callback, $dispatched);
        }
    }

    public function debugMatch($method, $uri)
    {
        $requestMethod = $method ? $method : 'GET';
        $requestUri = $this->normalizeUri($uri);

        foreach ($this->routes as $route) {
            list($methods, $pattern, $callback) = $route;
            if (!$this->matchMethod($methods, $requestMethod)) {
                continue;
            }
            if ($this->matchRoute($pattern, $requestUri, $params)) {
                return array(
                    'matched' => true,
                    'pattern' => $pattern,
                    'handler' => $this->describeCallback($callback),
                    'params' => $params,
                );
            }
        }

        return array(
            'matched' => false,
            'pattern' => null,
            'handler' => null,
            'params' => array(),
        );
    }

    private function map($method, $pattern, $callback)
    {
        $pattern = $this->namespace . $pattern;
        $this->routes[] = array($method, $pattern, $callback);
        return $this;
    }

    private function getCurrentUri()
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        return $this->normalizeUri($uri);
    }

    private function normalizeUri($uri)
    {
        $path = parse_url($uri, PHP_URL_PATH);
        if ($path === null || $path === false) {
            $path = '/';
        }

        if ($this->basePath && strpos($path, $this->basePath) === 0) {
            $path = substr($path, strlen($this->basePath));
        }

        if ($path === '') {
            $path = '/';
        }

        return $path;
    }

    private function matchMethod($methods, $requestMethod)
    {
        return preg_match('#^(' . $methods . ')$#i', $requestMethod);
    }

    private function describeCallback($callback)
    {
        if (is_string($callback)) {
            return $callback;
        }

        if (is_array($callback) && count($callback) === 2) {
            $class = is_object($callback[0]) ? get_class($callback[0]) : $callback[0];
            return $class . '::' . $callback[1];
        }

        if ($callback instanceof \Closure) {
            return 'closure';
        }

        return 'callable';
    }

    private function matchRoute($route, $uri, &$params = array())
    {
        $route = str_replace('/', '\/', $route);
        $route = preg_replace('/\(\?P\<([a-zA-Z][a-zA-Z0-9_]*)\>/', '(?P<$1>', $route);
        $route = '#^' . $route . '$#';

        if (preg_match($route, $uri, $matches)) {
            foreach ($matches as $key => $value) {
                if (!is_int($key)) {
                    $params[$key] = $value;
                }
            }
            return true;
        }

        return false;
    }
}
