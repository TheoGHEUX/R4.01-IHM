<?php

declare(strict_types=1);

/**
 * Point d’entrée de l’application.
 *
 * Il délègue le démarrage de l’application à {@see src/app.php} :
 * - bootstrap (chargement des classes/fonctions)
 * - construction des dépendances (gateways, use cases, renderer, controllers)
 * - définition des routes
 * - dispatch de la requête HTTP courante
 *
 * @see src/app.php
 */

require __DIR__ . '/../src/app.php';