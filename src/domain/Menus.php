<?php

declare(strict_types=1);

class MenusDomain
{
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

