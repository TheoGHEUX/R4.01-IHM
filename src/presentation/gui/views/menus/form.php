<section class="card">
    <h1><?= $mode === 'edit' ? 'Modifier un menu' : 'Creer un menu' ?></h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert--error">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= h((string)$e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php $action = $mode === 'edit' ? route_url('/menus/' . $menuId . '/update') : route_url('/menus'); ?>
    <form method="post" action="<?= h($action) ?>" class="form">
        <div class="form__row">
            <label for="nom">Nom du menu</label>
            <input id="nom" name="nom" type="text" value="<?= h((string)$nom) ?>" required />
        </div>

        <div class="form__row">
            <label for="createurId">Createur</label>
            <select id="createurId" name="createurId" required>
                <option value="">- Selectionner -</option>
                <?php foreach ($utilisateurs as $u): ?>
                    <?php
                    $uid = (string)($u['id'] ?? '');
                    $label = trim((string)($u['prenom'] ?? '') . ' ' . (string)($u['nom'] ?? ''));
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
                    $checked = in_array($pid, array_map('strval', (array)$selectedPlatIds), true);
                    ?>
                    <label class="choice">
                        <input type="checkbox" name="platIds[]" value="<?= h($pid) ?>" <?= $checked ? 'checked' : '' ?> />
                        <span><?= h((string)($p['nom'] ?? '-')) ?> - <?= h((string)($p['prix'] ?? '-')) ?> EUR</span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form__actions">
            <button class="btn" type="submit"><?= $mode === 'edit' ? 'Enregistrer' : 'Creer le menu' ?></button>
            <a class="btn btn--ghost" href="<?= h(route_url('/menus')) ?>">Annuler</a>
        </div>
    </form>

    <?php if ($mode === 'edit'): ?>
        <form method="post" action="<?= h(route_url('/menus/' . $menuId . '/delete')) ?>" style="margin-top:10px;">
            <button class="btn btn--danger" type="submit" onclick="return confirm('Confirmer la suppression de ce menu ?');">Supprimer</button>
        </form>
    <?php endif; ?>
</section>

