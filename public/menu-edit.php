<?php

$pageTitle = "Modifier un menu";
$logoPath = "/assets/logo.png";
require __DIR__ . '/../templates/header.php';

$config = require __DIR__ . '/../src/config.php';

require_once __DIR__ . '/../src/Support/helpers.php';
require_once __DIR__ . '/../src/http.php';
require_once __DIR__ . '/../src/Domain/Menu/MenuService.php';

$timeout = $config['http']['timeout'];
$menusBaseUrl = $config['services']['menus'];
$platsUsersBaseUrl = $config['services']['plats-utilisateurs'];

$id = (string)($_GET['id'] ?? '');
if ($id === '') {
    echo '<section class="card"><h1>Erreur</h1><p>Paramètre "id" manquant.</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

/** Menu **/
$menuRes = http_get($menusBaseUrl . '/menus/' . rawurlencode($id), $timeout);

if (!$menuRes['ok']) {
    echo '<section class="card"><h1>Erreur</h1><p>Erreur réseau (menu): ' . h($menuRes['error']) . '</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}
if ($menuRes['http_code'] < 200 || $menuRes['http_code'] >= 300) {
    echo '<section class="card"><h1>Erreur</h1><p>Menu introuvable (HTTP ' . (int)$menuRes['http_code'] . ').</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

$menu = json_decode($menuRes['body'], true);
if (!is_array($menu)) {
    echo '<section class="card"><h1>Erreur</h1><p>Réponse JSON invalide (menu).</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

/** Plats + utilisateurs **/
$platsRes = http_get($platsUsersBaseUrl . '/plats', $timeout);
$usersRes = http_get($platsUsersBaseUrl . '/utilisateurs', $timeout);

if (!$platsRes['ok'] || !$usersRes['ok']) {
    echo '<section class="card"><h1>Erreur</h1><p>Impossible de charger les données nécessaires.</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

$plats = json_decode($platsRes['body'], true);
$utilisateurs = json_decode($usersRes['body'], true);

if (!is_array($plats) || !is_array($utilisateurs)) {
    echo '<section class="card"><h1>Erreur</h1><p>Réponse JSON invalide depuis un service.</p></section>';
    require __DIR__ . '/../templates/footer.php';
    exit;
}

$errors = [];

// sticky (menu)
$nom = (string)($menu['nom'] ?? '');
$createurId = (string)($menu['createurId'] ?? '');
$selectedPlatIds = [];

if (isset($menu['plats']) && is_array($menu['plats'])) {
    foreach ($menu['plats'] as $p) {
        if (is_array($p) && isset($p['id'])) $selectedPlatIds[] = (string)$p['id'];
    }
}

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
            'id' => $menu['id'] ?? $id,
            'nom' => $nom,
            'createurId' => (int)$createurId,
            'createurNom' => $createurNom,
            'dateCreation' => $menu['dateCreation'] ?? today(),
            'dateMiseAJour' => today(),
            'plats' => $platsSelectionnes,
            'prixTotal' => $prixTotal,
        ];

        $putRes = http_put_json($menusBaseUrl . '/menus/' . rawurlencode($id), $payload, $timeout);

        if (!$putRes['ok']) {
            $errors[] = 'Erreur réseau lors de la modification: ' . $putRes['error'];
        } elseif ($putRes['http_code'] < 200 || $putRes['http_code'] >= 300) {
            $errors[] = 'Erreur HTTP ' . $putRes['http_code'] . ' lors de la modification.';
        } else {
            header('Location: /menus.php');
            exit;
        }
    }
}

?>
    <section class="card">
        <h1>Modifier un menu</h1>

        <?php if ($errors): ?>
            <div class="alert alert--error">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= h($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/menu-edit.php?id=<?= h($id) ?>" class="form">
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
                <button class="btn" type="submit">Enregistrer</button>
                <a class="btn btn--ghost" href="/menus.php">Annuler</a>
            </div>
        </form>
    </section>

<?php require __DIR__ . '/../templates/footer.php'; ?>