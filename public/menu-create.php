<?php

$pageTitle = "Créer un menu";
$logoPath = "/assets/logo.png";
require __DIR__ . '/../templates/header.php';

$config = require __DIR__ . '/../src/config.php';

require_once __DIR__ . '/../src/Support/helpers.php';
require_once __DIR__ . '/../src/http.php';
require_once __DIR__ . '/../src/Domain/Menu/MenuService.php';
require_once __DIR__ . '/../src/Api/JsonApi.php';

$timeout = $config['http']['timeout'];

$menusBaseUrl = $config['services']['menus'];
$platsUsersBaseUrl = $config['services']['plats-utilisateurs'];

/**
 * Chargement des données du formulaire
 */
$platsRes = api_get_json($platsUsersBaseUrl . '/plats', $timeout);
$usersRes = api_get_json($platsUsersBaseUrl . '/utilisateurs', $timeout);

if (!$platsRes['ok'] || !$usersRes['ok']) {
    echo '<section class="card"><h1>Erreur</h1><p>Impossible de charger les données nécessaires.</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

$plats = $platsRes['data'];
$utilisateurs = $usersRes['data'];

if (!is_array($plats) || !is_array($utilisateurs)) {
    echo '<section class="card"><h1>Erreur</h1><p>Réponse JSON invalide depuis un service.</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

$errors = [];

// sticky
$nom = '';
$createurId = '';
$selectedPlatIds = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim((string)($_POST['nom'] ?? ''));
    $createurId = (string)($_POST['createurId'] ?? '');
    $selectedPlatIds = $_POST['platIds'] ?? [];
    if (!is_array($selectedPlatIds)) $selectedPlatIds = [];

    if ($nom === '') $errors[] = 'Le nom du menu est obligatoire.';
    if ($createurId === '' || !ctype_digit((string)$createurId)) $errors[] = 'Le créateur est obligatoire.';
    if (count($selectedPlatIds) === 0) $errors[] = 'Sélectionne au moins un plat.';

    if (!$errors) {
        [$platsSelectionnes, $prixTotal] = build_menu_plats_and_total($plats, $selectedPlatIds);

        $createurNom = find_utilisateur_nom($utilisateurs, $createurId);
        if ($createurNom === null) $errors[] = "Créateur introuvable.";
    }

    if (!$errors) {
        $payload = [
            'nom' => $nom,
            'createurId' => (int)$createurId,
            'createurNom' => $createurNom,
            'dateCreation' => today(),
            'dateMiseAJour' => today(),
            'plats' => $platsSelectionnes,
            'prixTotal' => $prixTotal,
        ];

        $postRes = api_post_json($menusBaseUrl . '/menus', $payload, $timeout);

        if (!$postRes['ok']) {
            $errors[] = 'Erreur réseau lors de la création du menu: ' . $postRes['error'];
        } elseif ($postRes['http_code'] < 200 || $postRes['http_code'] >= 300) {
            $errors[] = 'Erreur HTTP ' . $postRes['http_code'] . ' lors de la création du menu.';
        } else {
            header('Location: /menus.php');
            exit;
        }
    }
}

?>
    <section class="card">
        <h1>Créer un menu</h1>

        <?php if ($errors): ?>
            <div class="alert alert--error">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= h($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/menu-create.php" class="form">
            <div class="form__row">
                <label for="nom">Nom du menu</label>
                <input id="nom" name="nom" type="text" value="<?= h($nom) ?>" required />
            </div>

            <div class="form__row">
                <label for="createurId">Créateur</label>
                <select id="createurId" name="createurId" required>
                    <option value="">— Sélectionner —</option>
                    <?php foreach ($utilisateurs as $u): ?>
                        <?php
                        $uid = (string)($u['id'] ?? '');
                        $label = trim(($u['prenom'] ?? '') . ' ' . ($u['nom'] ?? ''));
                        ?>
                        <option value="<?= h($uid) ?>" <?= $uid === (string)$createurId ? 'selected' : '' ?>>
                            <?= h($label) ?> (#<?= h($uid) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form__row">
                <label>Plats (au moins 1)</label>
                <div class="form__choices">
                    <?php foreach ($plats as $p): ?>
                        <?php
                        $pid = (string)($p['id'] ?? '');
                        $checked = in_array($pid, array_map('strval', $selectedPlatIds), true);
                        ?>
                        <label class="choice">
                            <input type="checkbox" name="platIds[]" value="<?= h($pid) ?>" <?= $checked ? 'checked' : '' ?> />
                            <span><?= h((string)($p['nom'] ?? '—')) ?> — <?= h((string)($p['prix'] ?? '—')) ?> €</span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form__actions">
                <button class="btn" type="submit">Créer le menu</button>
                <a class="btn btn--ghost" href="/menus.php">Annuler</a>
            </div>
        </form>
    </section>

<?php require __DIR__ . '/../templates/footer.php'; ?>