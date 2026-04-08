<?php

declare(strict_types=1);

/**
 * Cette interface définit les opérations nécessaires aux cas d'usage concernant
 * les plats, sans imposer la manière dont elles sont réalisées.
 */
interface PlatsInterface
{
    /**
     * Récupère la liste des plats.
     *
     * @return array Réponse JSON API (ok/http_code/data/error/raw).
     */
    public function listPlats(): array;

    /**
     * Récupère la liste des utilisateurs.
     *
     * @return array Réponse JSON API (ok/http_code/data/error/raw).
     */
    public function listUtilisateurs(): array;
}