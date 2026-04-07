<?php

declare(strict_types=1);

class PlatsDomain
{
    public static function normalizeCollection($payload): ?array
    {
        if (!is_array($payload)) {
            return null;
        }

        if (array_is_list($payload)) {
            return $payload;
        }

        $plats = $payload['plats'] ?? null;
        return is_array($plats) ? $plats : null;
    }
}

