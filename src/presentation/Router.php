<?php

declare(strict_types=1);

/**
 * Routeur HTTP.
 *
 * Fonctionnalités :
 * - enregistrement de routes GET/POST
 * - routes paramétrées via la syntaxe "/menus/{id}/edit"
 * - dispatch vers un handler callable : function(Request $r, ...$params)
 *
 */
class Router
{
    /**
     * Liste des routes enregistrées.
     *
     * Chaque entrée contient :
     * - method (string)
     * - path (string)
     * - regex (string)
     * - paramNames (string[])
     * - handler (callable)
     *
     * @var array<int, array{method:string, path:string, regex:string, paramNames:array<int, string>, handler:callable}>
     */
    private array $routes = [];

    /**
     * Enregistre une route GET.
     *
     * @param string $path Chemin de route (ex: "/menus/{id}/edit").
     * @param callable $handler Handler appelé si la route match.
     * @return void
     */
    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    /**
     * Enregistre une route POST.
     *
     * @param string $path Chemin de route.
     * @param callable $handler Handler appelé si la route match.
     * @return void
     */
    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    /**
     * Résout une requête et exécute le handler correspondant.
     *
     * - Compare la méthode HTTP
     * - Match le path via une regex construite depuis la route
     * - Extrait les paramètres nommés et les passe au handler
     *
     * En l'absence de route correspondante : {@see Response::notFound()}.
     *
     * @param Request $request Requête courante.
     * @return void
     */
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

    /**
     * Ajoute une route interne (utilisé par get/post).
     *
     * Transforme un chemin avec paramètres en regex :
     * - "/menus/{id}/edit" => "#^/menus/(?<id>[^/]+)/edit$#"
     *
     * @param string $method Méthode HTTP.
     * @param string $path Chemin de route.
     * @param callable $handler Handler.
     * @return void
     */
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