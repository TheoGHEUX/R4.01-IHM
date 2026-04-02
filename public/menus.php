<?php

$pageTitle = "Menus";
$logoPath = "/assets/logo.png";
require __DIR__ . '/../templates/header.php';

$config = require __DIR__ . '/../src/config.php';
require __DIR__ . '/../src/http.php';

$baseUrl = $config['services']['menus'];
$timeout = $config['http']['timeout'];

$url = $baseUrl . '/menus';
$result = http_get($url, $timeout);

if (!$result['ok']) {
    echo '<section class="card"><h1>Erreur</h1><p>Erreur cURL: ' . htmlspecialchars($result['error']) . '</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

$decoded = json_decode($result['body'], true);
if (!is_array($decoded)) {
    echo '<section class="card"><h1>Erreur</h1><p>Réponse JSON invalide.</p><pre>' . htmlspecialchars($result['body']) . '</pre></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

if (array_is_list($decoded)) {
    $menus = $decoded;
} else {
    $menus = $decoded['menus'] ?? null;
}

if (!is_array($menus)) {
    echo '<section class="card"><h1>Erreur</h1><p>Format inattendu : menus introuvables.</p><pre>' . htmlspecialchars($result['body']) . '</pre></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

function format_plats(array $plats): string {
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

?>
    <section class="card">
        <h1>Menus</h1>

        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Créateur</th>
                <th>Date création</th>
                <th>Dernière MAJ</th>
                <th>Plats</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($menus as $menu): ?>
                <tr>
                    <td><?= htmlspecialchars((string)($menu['id'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)($menu['nom'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php
                        $createurNom = $menu['createurNom'] ?? '—';
                        $createurId  = $menu['createurId'] ?? null;
                        $txt = (string)$createurNom;
                        if ($createurId !== null) $txt .= ' (#' . $createurId . ')';
                        echo htmlspecialchars($txt, ENT_QUOTES, 'UTF-8');
                        ?>
                    </td>
                    <td><?= htmlspecialchars((string)($menu['dateCreation'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)($menu['dateMiseAJour'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= format_plats($menu['plats'] ?? []) ?></td>
                    <td>
                        <?php
                        $total = $menu['prixTotal'] ?? null;
                        echo $total === null ? '—' : htmlspecialchars((string)$total, ENT_QUOTES, 'UTF-8') . ' €';
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

<?php require __DIR__ . '/../templates/footer.php'; ?>