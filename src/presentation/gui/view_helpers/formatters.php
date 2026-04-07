<?php


function format_plats(array $plats): string
{
    if ($plats === []) return '—';
    $items = [];
    foreach ($plats as $p) {
        if (!is_array($p)) continue;
        $nom = $p['nom'] ?? ('Plat #' . ($p['id'] ?? '—'));
        $prix = $p['prix'] ?? null;
        $chunk = $nom;
        if ($prix !== null) $chunk .= ' (' . $prix . ' €)';
        $items[] = $chunk;
    }
    return htmlspecialchars(implode(' ; ', $items), ENT_QUOTES, 'UTF-8');
}


function format_lignes(array $lignes): string
{
    if ($lignes === []) return '—';
    $items = [];
    foreach ($lignes as $l) {
        if (!is_array($l)) continue;
        $menuId = (string)($l['menuId'] ?? '—');
        $nom = $l['menuNom'] ?? ('Menu #' . $menuId);
        $qte = $l['quantite'] ?? '—';
        $prix = $l['prixLigne'] ?? null;
        $chunk = $nom . ' x' . $qte;
        if ($prix !== null) $chunk .= ' (' . $prix . ' €)';
        $items[] = $chunk;
    }
    return htmlspecialchars(implode(' ; ', $items), ENT_QUOTES, 'UTF-8');
}
