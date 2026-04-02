<?php

$pageTitle = "Commandes";
$logoPath = "/assets/logo.png";
require __DIR__ . '/../templates/header.php';

$config = require __DIR__ . '/../src/config.php';
require __DIR__ . '/../src/http.php';

$baseUrl = $config['services']['commandes'];
$timeout = $config['http']['timeout'];

$url = $baseUrl . '/commandes';
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
    $commandes = $decoded;
} else {
    $commandes = $decoded['commandes'] ?? null;
}

if (!is_array($commandes)) {
    echo '<section class="card"><h1>Erreur</h1><p>Format inattendu : commandes introuvables.</p><pre>' . htmlspecialchars($result['body']) . '</pre></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

function format_lignes(array $lignes): string {
    if ($lignes === []) return '—';
    $items = [];
    foreach ($lignes as $l) {
        if (!is_array($l)) continue;
        $nom = $l['menuNom'] ?? ('Menu #' . ($l['menuId'] ?? '—'));
        $qte = $l['quantite'] ?? '—';
        $prix = $l['prixLigne'] ?? null;
        $chunk = $nom . ' x' . $qte;
        if ($prix !== null) $chunk .= ' (' . $prix . ' €)';
        $items[] = $chunk;
    }
    return htmlspecialchars(implode(' ; ', $items), ENT_QUOTES, 'UTF-8');
}

?>
    <section class="card">
        <h1>Commandes</h1>

        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Abonné</th>
                <th>Date commande</th>
                <th>Date livraison</th>
                <th>Adresse livraison</th>
                <th>Lignes</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($commandes as $cmd): ?>
                <tr>
                    <td><?= htmlspecialchars((string)($cmd['id'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)($cmd['abonneId'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)($cmd['dateCommande'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)($cmd['dateLivraison'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)($cmd['adresseLivraison'] ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= format_lignes($cmd['lignes'] ?? []) ?></td>
                    <td>
                        <?php
                        $total = $cmd['prixTotal'] ?? null;
                        echo $total === null ? '—' : htmlspecialchars((string)$total, ENT_QUOTES, 'UTF-8') . ' €';
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

<?php require __DIR__ . '/../templates/footer.php'; ?>