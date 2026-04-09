<?php

/**
 * Fonctions utilitaires de l'application.
 *
 * Ce fichier contient des fonctions utilisées dans plusieurs couches :
 * - Presentation (vues/layout) : échappement HTML, génération d'URLs
 * - Controllers/UseCases : aide date, compatibilité PHP
 *
 */

/**
 * Échappe une chaîne pour un affichage HTML sûr.
 *
 * @param string $s Chaîne à échapper.
 * @return string Chaîne échappée (ENT_QUOTES, UTF-8).
 */
function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/**
 * Renvoie la date du jour au format YYYY-MM-DD.
 *
 * @return string Date du jour (ex: "2026-04-09").
 */
function today(): string
{
    return date('Y-m-d');
}

/**
 * Construit une URL interne de navigation en conservant un point d'entrée unique (/index.php).
 *
 * @param string $path Chemin logique de l'application (ex: "/", "/menus", "/menus/123/edit").
 * @return string URL navigable correspondant au point d'entrée.
 */
function route_url(string $path): string
{
    $normalized = '/' . ltrim($path, '/');
    if ($normalized === '/index.php') {
        return '/index.php';
    }

    if ($normalized === '/') {
        return '/index.php';
    }

    return '/index.php?route=' . rawurlencode($normalized);
}

if (!function_exists('array_is_list')) {
    function array_is_list(array $array): bool
    {
        $expected = 0;
        foreach ($array as $key => $_) {
            if ($key !== $expected) {
                if (!is_int($key)) {
                    if (!ctype_digit((string)$key) || (int)$key !== $expected) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
            $expected++;
        }
        return true;
    }
}