<?php

$pageTitle = "Créer une commande";
$logoPath = "/assets/logo.png";
require __DIR__ . '/../templates/header.php';

$config = require __DIR__ . '/../src/config.php';

require_once __DIR__ . '/../src/Support/helpers.php';
require_once __DIR__ . '/../src/Api/JsonApi.php';

$timeout = $config['http']['timeout'];

$commandesBaseUrl = $config['services']['commandes'];
$menusBaseUrl = $config['services']['menus'];
$platsUsersBaseUrl = $config['services']['plats-utilisateurs'];

/**
 * Chargement des données nécessaires au formulaire
 * - abonnés (utilisateurs)
 * - menus (pour choisir ce qu'on commande)
 */
$usersRes = api_get_json($platsUsersBaseUrl . '/utilisateurs', $timeout);
$menusRes = api_get_json($menusBaseUrl . '/menus', $timeout);

if (!$usersRes['ok'] || !$menusRes['ok']) {
    echo '<section class="card"><h1>Erreur</h1><p>Impossible de charger les données nécessaires.</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

$utilisateurs = $usersRes['data'];
$decodedMenus = $menusRes['data'];

/** menus: accepte [ ... ] ou { "menus": [ ... ] } (comme public/menus.php) */
if (is_array($decodedMenus) && array_is_list($decodedMenus)) {
    $menus = $decodedMenus;
} else {
    $menus = is_array($decodedMenus) ? ($decodedMenus['menus'] ?? null) : null;
}

if (!is_array($utilisateurs) || !is_array($menus)) {
    echo '<section class="card"><h1>Erreur</h1><p>Réponse JSON invalide depuis un service.</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

/** Index des menus par ID pour validation/lookup (ID string accepté) */
$menusById = [];
foreach ($menus as $m) {
    if (is_array($m) && isset($m['id'])) {
        $menusById[(string)$m['id']] = $m;
    }
}

$errors = [];

/** sticky */
$abonneId = '';
$adresseLivraison = '';
$dateLivraison = today();

/**
 * lignes côté formulaire :
 * - menuId[] : string (peut être "Gm-vhNk1eU8")
 * - quantite[] : string/int
 */
$selectedMenuIds = [];
$quantites = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $abonneId = (string)($_POST['abonneId'] ?? '');
    $adresseLivraison = trim((string)($_POST['adresseLivraison'] ?? ''));
    $dateLivraison = trim((string)($_POST['dateLivraison'] ?? today()));

    $selectedMenuIds = $_POST['menuId'] ?? [];
    $quantites = $_POST['quantite'] ?? [];

    if (!is_array($selectedMenuIds)) $selectedMenuIds = [];
    if (!is_array($quantites)) $quantites = [];

    if ($abonneId === '' || !ctype_digit((string)$abonneId)) $errors[] = "L'abonné est obligatoire.";
    if ($adresseLivraison === '') $errors[] = "L'adresse de livraison est obligatoire.";
    if ($dateLivraison === '') $errors[] = "La date de livraison est obligatoire.";

    // construire les lignes
    $lignes = [];
    $prixTotal = 0.0;

    // On parcourt les index (menuId[i], quantite[i])
    $count = max(count($selectedMenuIds), count($quantites));
    for ($i = 0; $i < $count; $i++) {
        $menuId = isset($selectedMenuIds[$i]) ? trim((string)$selectedMenuIds[$i]) : '';
        $qteRaw = isset($quantites[$i]) ? trim((string)$quantites[$i]) : '';

        // ligne vide => on ignore
        if ($menuId === '' && $qteRaw === '') continue;

        if ($menuId === '') {
            $errors[] = "Une ligne contient un menu invalide.";
            continue;
        }
        if ($qteRaw === '' || !ctype_digit((string)$qteRaw)) {
            $errors[] = "Une ligne contient une quantité invalide (entier attendu).";
            continue;
        }

        $qte = (int)$qteRaw;
        if ($qte <= 0) {
            $errors[] = "La quantité doit être > 0.";
            continue;
        }

        // IMPORTANT: menuId peut être une string (ex: Gm-vhNk1eU8)
        if (!isset($menusById[$menuId])) {
            $errors[] = "Menu #{$menuId} introuvable.";
            continue;
        }

        $menu = $menusById[$menuId];
        $menuNom = (string)($menu['nom'] ?? ('Menu #' . $menuId));
        $prixUnitaire = (float)($menu['prixTotal'] ?? 0);
        $prixLigne = round($prixUnitaire * $qte, 2);

        $lignes[] = [
            'menuId' => $menuId,          // <-- string, pas int
            'menuNom' => $menuNom,
            'quantite' => $qte,
            'prixUnitaire' => $prixUnitaire,
            'prixLigne' => $prixLigne,
        ];

        $prixTotal += $prixLigne;
    }

    if (!$errors && count($lignes) === 0) {
        $errors[] = "Ajoute au moins une ligne de commande.";
    }

    if (!$errors) {
        $payload = [
            'abonneId' => (int)$abonneId,
            'dateCommande' => today(),
            'dateLivraison' => $dateLivraison,
            'adresseLivraison' => $adresseLivraison,
            'lignes' => $lignes,
            'prixTotal' => round($prixTotal, 2),
        ];

        $postRes = api_post_json($commandesBaseUrl . '/commandes', $payload, $timeout);

        if (!$postRes['ok']) {
            $errors[] = 'Erreur réseau lors de la création de la commande: ' . $postRes['error'];
        } elseif ($postRes['http_code'] < 200 || $postRes['http_code'] >= 300) {
            $raw = is_string($postRes['raw'] ?? null) ? trim((string)$postRes['raw']) : '';
            $errors[] = 'Erreur HTTP ' . $postRes['http_code'] . ' lors de la création de la commande.'
                . ($raw !== '' ? ' (réponse: ' . h($raw) . ')' : '');
        } else {
            header('Location: /commandes.php');
            exit;
        }
    }
}

?>
    <section class="card">
        <h1>Créer une commande</h1>

        <?php if ($errors): ?>
            <div class="alert alert--error">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= h($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/commande-create.php" class="form">
            <div class="form__row">
                <label for="abonneId">Abonné</label>
                <select id="abonneId" name="abonneId" required>
                    <option value="">— Sélectionner —</option>
                    <?php foreach ($utilisateurs as $u): ?>
                        <?php
                        $uid = (string)($u['id'] ?? '');
                        $label = trim(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? ''));
                        ?>
                        <option value="<?= h($uid) ?>" <?= $uid === (string)$abonneId ? 'selected' : '' ?>>
                            <?= h($label) ?> (#<?= h($uid) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form__row">
                <label for="dateLivraison">Date de livraison</label>
                <input id="dateLivraison" name="dateLivraison" type="date" value="<?= h($dateLivraison) ?>" required />
            </div>

            <div class="form__row">
                <label for="adresseLivraison">Adresse de livraison</label>
                <input id="adresseLivraison" name="adresseLivraison" type="text" value="<?= h($adresseLivraison) ?>" required />
            </div>

            <div class="form__row">
                <label>Lignes de commande</label>
                <p style="margin: 0 0 10px 0; opacity: .8;">Ajoute 1 ou plusieurs lignes (menu + quantité).</p>

                <?php
                // On affiche 3 lignes par défaut, et on conserve les lignes postées (sticky)
                $existingCount = max(3, count($selectedMenuIds), count($quantites));
                for ($i = 0; $i < $existingCount; $i++):
                    $menuIdVal = isset($selectedMenuIds[$i]) ? (string)$selectedMenuIds[$i] : '';
                    $qteVal = isset($quantites[$i]) ? (string)$quantites[$i] : '';
                    ?>
                    <div class="form__row" style="display:flex; gap:10px; align-items:end;">
                        <div style="flex:1;">
                            <label for="menuId_<?= $i ?>">Menu</label>
                            <select id="menuId_<?= $i ?>" name="menuId[]">
                                <option value="">—</option>
                                <?php foreach ($menus as $m): ?>
                                    <?php
                                    $mid = (string)($m['id'] ?? '');
                                    $mnom = (string)($m['nom'] ?? ('Menu #' . $mid));
                                    $mprix = $m['prixTotal'] ?? null;
                                    $lbl = $mnom . ($mprix !== null ? ' — ' . $mprix . ' €' : '');
                                    ?>
                                    <option value="<?= h($mid) ?>" <?= $mid === $menuIdVal ? 'selected' : '' ?>>
                                        <?= h($lbl) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div style="width:140px;">
                            <label for="quantite_<?= $i ?>">Quantité</label>
                            <input id="quantite_<?= $i ?>" name="quantite[]" type="number" min="1" step="1" value="<?= h($qteVal) ?>" />
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="form__actions">
                <button class="btn" type="submit">Créer la commande</button>
                <a class="btn btn--ghost" href="/commandes.php">Annuler</a>
            </div>
        </form>
    </section>

<?php require __DIR__ . '/../templates/footer.php'; ?>