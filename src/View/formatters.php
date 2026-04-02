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