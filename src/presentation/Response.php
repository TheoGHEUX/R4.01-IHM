<?php

declare(strict_types=1);

class Response
{
    public static function redirect(string $path): void
    {
        header('Location: ' . route_url($path));
        exit;
    }

    public static function notFound(): void
    {
        http_response_code(404);
        echo '<section class="card"><h1>404</h1><p>Route introuvable.</p></section>';
    }
}

