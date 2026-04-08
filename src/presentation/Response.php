<?php

declare(strict_types=1);

/**
 * Helpers de réponse HTTP.
 *
 * Cette classe centralise des actions de réponse très simples :
 * - redirection HTTP (Location)
 * - réponse 404 minimale
 */
class Response
{
    /**
     * Redirige vers un chemin interne de l'application.
     *
     * @param string $path Chemin cible (ex: "/menus").
     * @return void
     */
    public static function redirect(string $path): void
    {
        header('Location: ' . route_url($path));
        exit;
    }

    /**
     * Retourne une réponse 404 simple.
     *
     * @return void
     */
    public static function notFound(): void
    {
        http_response_code(404);
        echo '<section class="card"><h1>404</h1><p>Route introuvable.</p></section>';
    }
}