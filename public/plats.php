<?php

$pageTitle = "Plats";
$logoPath = "/assets/logo.png";
require __DIR__ . '/../templates/header.php';

$config = require __DIR__ . '/../src/config.php';
require __DIR__ . '/../src/http.php';

$baseUrl = $config['services']['plats-utilisateurs'];
$timeout = $config['http']['timeout'];

$url = $baseUrl . '/plats';
$result = http_get($url, $timeout);

if (!$result['ok']) {
    echo '<section class="card"><h1>Erreur</h1><p>Erreur cURL: ' . htmlspecialchars($result['error']) . '</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

$data = json_decode($result['body'], true);
if (!is_array($data)) {
    echo '<section class="card"><h1>Erreur</h1><p>Réponse JSON invalide.</p><pre>' . htmlspecialchars($result['body']) . '</pre></section>';
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
                    <td><?= htmlspecialchars($plat['nom'] ?? $plat['name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($plat['description'] ?? '—') ?></td>
                    <td>
                        <?php
                        $prix = $plat['prix'] ?? $plat['price'] ?? null;
                        echo $prix === null ? '—' : htmlspecialchars((string)$prix) . ' €';
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

<?php require __DIR__ . '/../templates/footer.php'; ?>