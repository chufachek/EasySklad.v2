<?php
namespace Core;

class Router
{
    private $routes = array();
    private $globalMiddleware = array();

    public function addGlobalMiddleware($middleware)
    {
        $this->globalMiddleware[] = $middleware;
    }

    public function add($method, $pattern, $handler, $middlewareList = array())
    {
        $this->routes[] = array(
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middlewareList,
        );
    }

    public function dispatch(Request $request)
    {
        $path = $request->getPath();
        $method = $request->getMethod();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            $params = $this->match($route['pattern'], $path);
            if ($params !== false) {
                $request->setParams($params);
                $middlewareStack = array_merge($this->globalMiddleware, $route['middleware']);
                $this->runMiddleware($middlewareStack, $request, $route['handler']);
                return;
            }
        }

        Response::error('NOT_FOUND', 'Route not found', 404);
    }

    private function match($pattern, $path)
    {
        $regex = preg_replace('#:([a-zA-Z0-9_]+)#', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        if (preg_match($regex, $path, $matches)) {
            $params = array();
            foreach ($matches as $key => $value) {
                if (!is_int($key)) {
                    $params[$key] = $value;
                }
            }
            return $params;
        }
        return false;
    }

    private function runMiddleware($middlewareStack, Request $request, $handler)
    {
        $next = function () use ($handler, $request) {
            call_user_func($handler, $request);
        };

        while ($middleware = array_pop($middlewareStack)) {
            $next = $this->makeNext($middleware, $next, $request);
        }

        $next();
    }

    private function makeNext($middleware, $next, Request $request)
    {
        return function () use ($middleware, $next, $request) {
            $middleware->handle($request, $next);
        };
    }
}
