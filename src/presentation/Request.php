<?php

declare(strict_types=1);

class Request
{
    private string $method;
    private string $path;
    private array $body;

    public function __construct(string $method, string $path, array $body)
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->body = $body;
    }

    public static function fromGlobals(): self
    {
        $method = (string)($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $body = $_POST;

        $route = $_GET['route'] ?? null;
        if (is_string($route) && $route !== '') {
            $path = rawurldecode($route);
        } else {
            $uri = (string)($_SERVER['REQUEST_URI'] ?? '/');
            $path = (string)(parse_url($uri, PHP_URL_PATH) ?? '/');
            if ($path === '/index.php') {
                $path = '/';
            }
        }

        $path = self::normalizePath($path);

        return new self($method, $path, $body);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function post(string $key, $default = null)
    {
        return $this->body[$key] ?? $default;
    }

    public function postArray(string $key): array
    {
        $value = $this->body[$key] ?? [];
        return is_array($value) ? $value : [];
    }

    private static function normalizePath(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        if ($path !== '/' && str_ends_with($path, '/')) {
            return rtrim($path, '/');
        }

        return $path;
    }
}
