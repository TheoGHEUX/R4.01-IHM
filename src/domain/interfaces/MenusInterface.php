<?php

declare(strict_types=1);

/**
 * Cette interface définit les opérations nécessaires aux cas d'usage concernant
 * les menus, sans imposer la manière dont elles sont réalisées.
 *
 */
interface MenusInterface
{
    /**
     * Récupère la liste des menus.
     *
     * @return array Réponse JSON API (ok/http_code/data/error/raw).
     */
    public function listMenus(): array;

    /**
     * Récupère un menu par identifiant.
     *
     * @param string $id Identifiant du menu.
     * @return array Réponse JSON API (ok/http_code/data/error/raw).
     */
    public function getMenu(string $id): array;

    /**
     * Crée un menu.
     *
     * @param array $payload Données du menu à créer.
     * @return array Réponse JSON API (ok/http_code/data/error/raw).
     */
    public function createMenu(array $payload): array;

    /**
     * Met à jour un menu existant.
     *
     * @param string $id Identifiant du menu.
     * @param array $payload Données complètes du menu.
     * @return array Réponse JSON API (ok/http_code/data/error/raw).
     */
    public function updateMenu(string $id, array $payload): array;

    /**
     * Supprime un menu.
     *
     * @param string $id Identifiant du menu.
     * @return array Réponse JSON API (ok/http_code/data/error/raw).
     */
    public function deleteMenu(string $id): array;
}