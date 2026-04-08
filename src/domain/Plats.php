<?php

declare(strict_types=1);

/**
 * Domaine "Plats".
 *
 */
class PlatsDomain
{
    /**
     * Normalise une collection de plats provenant de l'API.
     *
     * @param mixed $payload Données décodées depuis JSON.
     * @return array<int, array>|null Liste de plats si format reconnu, sinon null.
     */
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