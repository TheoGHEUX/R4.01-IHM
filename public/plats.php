<?php

$pageTitle = "Plats";
$logoPath = "/assets/logo.png";
require __DIR__ . '/../templates/header.php';

$config = require __DIR__ . '/../src/config.php';
require __DIR__ . '/../src/http.php';
require_once __DIR__ . '/../src/Support/helpers.php';
require_once __DIR__ . '/../src/Api/JsonApi.php';

$baseUrl = $config['services']['plats-utilisateurs'];
$timeout = $config['http']['timeout'];

$url = $baseUrl . '/plats';
$res = api_get_json($url, $timeout);

if (!$res['ok']) {
    echo '<section class="card"><h1>Erreur</h1><p>Erreur API: ' . h($res['error']) . '</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

$data = $res['data'];
if (!is_array($data)) {
    echo '<section class="card"><h1>Erreur</h1><p>Réponse JSON invalide.</p><pre>' . h($res['raw'] ?? '') . '</pre></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}
?>

    <section class="card">
        <h1>Liste des plats</h1>

        <table class="table">
            <thead>
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Prix</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $plat): ?>
                <tr>
                    <td><?= h((string)($plat['nom'] ?? $plat['name'] ?? '—')) ?></td>
                    <td><?= h((string)($plat['description'] ?? '—')) ?></td>
                    <td>
                        <?php
                        $prix = $plat['prix'] ?? $plat['price'] ?? null;
                        echo $prix === null ? '—' : h((string)$prix) . ' €';
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

<?php require __DIR__ . '/../templates/footer.php'; ?>