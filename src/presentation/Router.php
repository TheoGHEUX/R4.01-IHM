<?php

declare(strict_types=1);

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $path = $request->path();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $matches = [];
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            $params = [];
            foreach ($route['paramNames'] as $paramName) {
                $params[] = $matches[$paramName] ?? null;
            }

            call_user_func($route['handler'], $request, ...$params);
            return;
        }

        Response::notFound();
    }

    private function add(string $method, string $path, callable $handler): void
    {
        $paramNames = [];
        $pattern = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            static function (array $match) use (&$paramNames): string {
                $paramNames[] = $match[1];
                return '(?<' . $match[1] . '>[^/]+)';
            },
            $path
        );

        $regex = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'regex' => $regex,
            'paramNames' => $paramNames,
            'handler' => $handler,
        ];
    }
}

