<?php

declare(strict_types=1);

/**
 * Cette interface définit les opérations nécessaires aux cas d'usage concernant
 * les commandes, sans imposer la manière dont elles sont réalisées.
 *
 */
interface CommandesInterface
{
    /**
     * Récupère la liste des commandes.
     *
     * @return array Réponse structurée de type JSON API (pattern utilisé dans le projet) :
     *               - ok (bool)
     *               - http_code (int)
     *               - data (mixed)
     *               - error (string|null)
     *               - raw (string|null)
     */
    public function listCommandes(): array;

    /**
     * Crée une commande.
     *
     * @param array $payload Données de commande à transmettre à l'API.
     * @return array Réponse structurée JSON API (ok/http_code/data/error/raw).
     */
    public function createCommande(array $payload): array;
}