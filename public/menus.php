<?php

$pageTitle = "Menus";
$logoPath = "/assets/logo.png";
require __DIR__ . '/../templates/header.php';

$config = require __DIR__ . '/../src/config.php';

require_once __DIR__ . '/../src/Support/helpers.php';
require_once __DIR__ . '/../src/Api/JsonApi.php';
require_once __DIR__ . '/../src/View/formatters.php';

$baseUrl = $config['services']['menus'];
$timeout = $config['http']['timeout'];

$res = api_get_json($baseUrl . '/menus', $timeout);

if (!$res['ok']) {
    echo '<section class="card"><h1>Erreur</h1><p>Erreur API: ' . h($res['error']) . '</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

$decoded = $res['data'];

// Accepte: [ ... ] ou { "menus": [ ... ] }
if (array_is_list($decoded)) {
    $menus = $decoded;
} else {
    $menus = $decoded['menus'] ?? null;
}

if (!is_array($menus)) {
    echo '<section class="card"><h1>Erreur</h1><p>Format inattendu : menus introuvables.</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

?>
    <a class="btn" href="/menu-create.php">+ Créer un menu</a>

    <section class="card">
        <h1>Menus</h1>

        <table class="table">
            <thead>
            <tr>
                <th>Nom</th>
                <th>Créateur</th>
                <th>Date création</th>
                <th>Dernière MAJ</th>
                <th>Plats</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($menus as $menu): ?>
                <tr>
                    <td><?= h((string)($menu['nom'] ?? '—')) ?></td>
                    <td>
                        <?php
                        $createurNom = $menu['createurNom'] ?? '—';
                        $createurId  = $menu['createurId'] ?? null;
                        $txt = (string)$createurNom;
                        if ($createurId !== null) $txt .= ' (#' . $createurId . ')';
                        echo h($txt);
                        ?>
                    </td>
                    <td><?= h((string)($menu['dateCreation'] ?? '—')) ?></td>
                    <td><?= h((string)($menu['dateMiseAJour'] ?? '—')) ?></td>
                    <td><?= format_plats($menu['plats'] ?? []) ?></td>
                    <td>
                        <?php
                        $total = $menu['prixTotal'] ?? null;
                        echo $total === null ? '—' : h((string)$total) . ' €';
                        ?>
                    </td>
                    <td>
                        <?php $id = (string)($menu['id'] ?? ''); ?>
                        <?php if ($id !== ''): ?>
                            <a class="btn btn--ghost" href="/menu-edit.php?id=<?= h($id) ?>">Modifier</a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

<?php require __DIR__ . '/../templates/footer.php'; ?>