<?php

declare(strict_types=1);

class CommandesDomain
{
    public static function normalizeCollection($payload): ?array
    {
        if (!is_array($payload)) {
            return null;
        }

        if (array_is_list($payload)) {
            return $payload;
        }

        $commandes = $payload['commandes'] ?? null;
        return is_array($commandes) ? $commandes : null;
    }

    public static function validateInput(string $abonneId, string $adresseLivraison, string $dateLivraison): array
    {
        $errors = [];

        if ($abonneId === '' || !ctype_digit($abonneId)) {
            $errors[] = "L'abonne est obligatoire.";
        }

        if ($adresseLivraison === '') {
            $errors[] = "L'adresse de livraison est obligatoire.";
        }

        if ($dateLivraison === '') {
            $errors[] = 'La date de livraison est obligatoire.';
        }

        return $errors;
    }

    public static function indexMenusById(array $menus): array
    {
        $byId = [];
        foreach ($menus as $menu) {
            if (is_array($menu) && isset($menu['id'])) {
                $byId[(string)$menu['id']] = $menu;
            }
        }

        return $byId;
    }

    public static function buildLignesAndTotal(array $menusById, array $selectedMenuIds, array $quantites): array
    {
        $errors = [];
        $lignes = [];
        $total = 0.0;

        $count = max(count($selectedMenuIds), count($quantites));
        for ($i = 0; $i < $count; $i++) {
            $menuId = isset($selectedMenuIds[$i]) ? trim((string)$selectedMenuIds[$i]) : '';
            $qteRaw = isset($quantites[$i]) ? trim((string)$quantites[$i]) : '';

            if ($menuId === '' && $qteRaw === '') {
                continue;
            }

            if ($menuId === '') {
                $errors[] = 'Une ligne contient un menu invalide.';
                continue;
            }

            if ($qteRaw === '' || !ctype_digit($qteRaw)) {
                $errors[] = 'Une ligne contient une quantite invalide (entier attendu).';
                continue;
            }

            $qte = (int)$qteRaw;
            if ($qte <= 0) {
                $errors[] = 'La quantite doit etre > 0.';
                continue;
            }

            if (!isset($menusById[$menuId])) {
                $errors[] = 'Menu #' . $menuId . ' introuvable.';
                continue;
            }

            $menu = $menusById[$menuId];
            $menuNom = (string)($menu['nom'] ?? ('Menu #' . $menuId));
            $prixUnitaire = (float)($menu['prixTotal'] ?? 0);
            $prixLigne = round($prixUnitaire * $qte, 2);

            $lignes[] = [
                'menuId' => $menuId,
                'menuNom' => $menuNom,
                'quantite' => $qte,
                'prixUnitaire' => $prixUnitaire,
                'prixLigne' => $prixLigne,
            ];
            $total += $prixLigne;
        }

        if (!$errors && count($lignes) === 0) {
            $errors[] = 'Ajoute au moins une ligne de commande.';
        }

        return [$lignes, round($total, 2), $errors];
    }
}

