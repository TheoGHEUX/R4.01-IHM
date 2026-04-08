<?php

declare(strict_types=1);

/**
 * Domaine "Menus" (logique métier et règles de validation).
 *
 * Cette classe regroupe des fonctions utilisées par les UseCases liés aux menus.
 *
 */
class MenusDomain
{
    /**
     * Normalise une collection de menus provenant de l'API.
     *
     * @param mixed $payload Données décodées depuis JSON.
     * @return array<int, array>|null Liste de menus si format reconnu, sinon null.
     */
    public static function normalizeCollection($payload): ?array
    {
        if (!is_array($payload)) {
            return null;
        }

        if (array_is_list($payload)) {
            return $payload;
        }

        $menus = $payload['menus'] ?? null;
        return is_array($menus) ? $menus : null;
    }

    /**
     * Valide les champs du formulaire menu.
     *
     * @param string $nom Nom du menu.
     * @param string $createurId Identifiant du créateur (attendu numérique côté IHM).
     * @param array<int, mixed> $selectedPlatIds Liste des IDs de plats sélectionnés.
     * @return string[] Liste de messages d'erreur.
     */
    public static function validateInput(string $nom, string $createurId, array $selectedPlatIds): array
    {
        $errors = [];

        if ($nom === '') {
            $errors[] = 'Le nom du menu est obligatoire.';
        }

        if ($createurId === '' || !ctype_digit($createurId)) {
            $errors[] = 'Le createur est obligatoire.';
        }

        if (count($selectedPlatIds) === 0) {
            $errors[] = 'Selectionne au moins un plat.';
        }

        return $errors;
    }

    /**
     * À partir d'une liste de plats et d'IDs sélectionnés, reconstruit :
     * - la liste des plats sélectionnés (id, nom, prix)
     * - le total des prix (somme)
     *
     * @param array<int, array> $plats Liste complète des plats.
     * @param array<int, mixed> $selectedPlatIds IDs de plats sélectionnés.
     *
     * @return array{
     *   0: array<int, array{id:int, nom:string, prix:float}>,
     *   1: float
     * }
     *   - [0] plats sélectionnés
     *   - [1] total arrondi
     */
    public static function buildSelectedPlatsAndTotal(array $plats, array $selectedPlatIds): array
    {
        $platsById = [];
        foreach ($plats as $plat) {
            if (isset($plat['id'])) {
                $platsById[(string)$plat['id']] = $plat;
            }
        }

        $selected = [];
        $total = 0.0;

        foreach ($selectedPlatIds as $platId) {
            $id = (string)$platId;
            if (!isset($platsById[$id])) {
                continue;
            }

            $plat = $platsById[$id];
            $price = (float)($plat['prix'] ?? 0);

            $selected[] = [
                'id' => (int)$plat['id'],
                'nom' => (string)($plat['nom'] ?? ''),
                'prix' => $price,
            ];
            $total += $price;
        }

        return [$selected, round($total, 2)];
    }

    /**
     * Retrouve le nom complet (prénom + nom) d'un utilisateur à partir de son id.
     *
     * @param array<int, array> $utilisateurs Liste d'utilisateurs.
     * @param string $id Identifiant de l'utilisateur à chercher.
     * @return string|null Nom complet si trouvé et non vide, sinon null.
     */
    public static function findUtilisateurNom(array $utilisateurs, string $id): ?string
    {
        foreach ($utilisateurs as $utilisateur) {
            if ((string)($utilisateur['id'] ?? '') !== $id) {
                continue;
            }

            $prenom = trim((string)($utilisateur['prenom'] ?? ''));
            $nom = trim((string)($utilisateur['nom'] ?? ''));
            $fullName = trim($prenom . ' ' . $nom);
            return $fullName !== '' ? $fullName : null;
        }

        return null;
    }
}