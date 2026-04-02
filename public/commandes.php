<?php

$pageTitle = "Commandes";
$logoPath = "/assets/logo.png";
require __DIR__ . '/../templates/header.php';

$config = require __DIR__ . '/../src/config.php';

require_once __DIR__ . '/../src/Support/helpers.php';
require_once __DIR__ . '/../src/Api/JsonApi.php';
require_once __DIR__ . '/../src/View/formatters.php';

$baseUrl = $config['services']['commandes'];
$timeout = $config['http']['timeout'];

$res = api_get_json($baseUrl . '/commandes', $timeout);

if (!$res['ok']) {
    echo '<section class="card"><h1>Erreur</h1><p>Erreur API: ' . h($res['error']) . '</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

$decoded = $res['data'];

if (array_is_list($decoded)) {
    $commandes = $decoded;
} else {
    $commandes = $decoded['commandes'] ?? null;
}

if (!is_array($commandes)) {
    echo '<section class="card"><h1>Erreur</h1><p>Format inattendu : commandes introuvables.</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

/**
 * TODO (propre): déplacer dans src/View/formatters.php
 */
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
                    <td><?= h((string)($cmd['id'] ?? '—')) ?></td>
                    <td><?= h((string)($cmd['abonneId'] ?? '—')) ?></td>
                    <td><?= h((string)($cmd['dateCommande'] ?? '—')) ?></td>
                    <td><?= h((string)($cmd['dateLivraison'] ?? '—')) ?></td>
                    <td><?= h((string)($cmd['adresseLivraison'] ?? '—')) ?></td>
                    <td><?= format_lignes($cmd['lignes'] ?? []) ?></td>
                    <td>
                        <?php
                        $total = $cmd['prixTotal'] ?? null;
                        echo $total === null ? '—' : h((string)$total) . ' €';
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

<?php require __DIR__ . '/../templates/footer.php'; ?>