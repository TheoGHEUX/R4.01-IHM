<?php

function build_menu_plats_and_total(array $plats, array $selectedPlatIds): array {
    $platsById = [];
    foreach ($plats as $p) {
        if (isset($p['id'])) $platsById[(string)$p['id']] = $p;
    }

    $platsSelectionnes = [];
    $prixTotal = 0.0;

    foreach ($selectedPlatIds as $pid) {
        $pid = (string)$pid;
        if (!isset($platsById[$pid])) continue;
        $p = $platsById[$pid];

        $platsSelectionnes[] = [
            'id' => (int)$p['id'],
            'nom' => (string)($p['nom'] ?? ''),
            'prix' => (float)($p['prix'] ?? 0),
        ];
        $prixTotal += (float)($p['prix'] ?? 0);
    }

    return [$platsSelectionnes, round($prixTotal, 2)];
}

function find_utilisateur_nom(array $utilisateurs, string $id): ?string {
    foreach ($utilisateurs as $u) {
        if ((string)($u['id'] ?? '') === $id) return (string)($u['nom'] ?? '');
    }
    return null;
}