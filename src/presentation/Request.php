<?php

declare(strict_types=1);

/**
 * Représente une requête HTTP simplifiée pour l'application.
 *
 * Cette classe encapsule :
 * - la méthode HTTP (GET/POST/...)
 * - le chemin demandé (path) normalisé
 * - le body
 *
 */
class Request
{
    private string $method;
    private string $path;
    private array $body;

    /**
     * @param string $method Méthode HTTP brute.
     * @param string $path Chemin (URI path) normalisé.
     * @param array $body Corps de requête (typiquement $_POST).
     */
    public function __construct(string $method, string $path, array $body)
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->body = $body;
    }

    /**
     * Construit une instance à partir des superglobales PHP.
     *
     * @return self
     */
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

    /**
     * @return string Méthode HTTP en majuscules.
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * @return string Chemin normalisé (ex: "/menus", "/menus/123/edit").
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Accès à une valeur POST (similaire à $_POST[$key] ?? $default).
     *
     * @param string $key Nom du champ.
     * @param mixed $default Valeur par défaut si absent.
     * @return mixed
     */
    public function post(string $key, $default = null)
    {
        return $this->body[$key] ?? $default;
    }

    /**
     * Accès à un champ POST attendu comme tableau.
     *
     * @param string $key Nom du champ.
     * @return array Renvoie un tableau vide si absent ou si la valeur n'est pas un tableau.
     */
    public function postArray(string $key): array
    {
        $value = $this->body[$key] ?? [];
        return is_array($value) ? $value : [];
    }

    /**
     * Normalise un chemin :
     * - chaîne vide => "/"
     * - supprime le "/" final (sauf pour "/")
     *
     * @param string $path Chemin brut.
     * @return string Chemin normalisé.
     */
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