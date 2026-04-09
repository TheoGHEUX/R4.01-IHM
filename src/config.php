<?php

/**
 * Configuration de l'application.
 *
 * Ce fichier retourne un tableau PHP utilisé par {@see src/app.php} pour configurer :
 * - les URLs de base des microservices
 * - la configuration HTTP (timeout)
 *
 * @return array{
 *   services: array{
 *     plats-utilisateurs: string,
 *     menus: string,
 *     commandes: string
 *   },
 *   http: array{
 *     timeout: int
 *   }
 * }
 */
return [
    'services' => [
        'plats-utilisateurs' => 'http://localhost:3003',
        'menus'              => 'http://localhost:3004',
        'commandes'          => 'http://localhost:3005',
    ],
    'http' => [
        'timeout' => 5, // secondes
    ],
];