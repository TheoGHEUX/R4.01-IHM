<section class="card">
    <h1>Creer une commande</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert--error">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= h((string)$e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= h(route_url('/commandes')) ?>" class="form">
        <div class="form__row">
            <label for="abonneId">Abonne</label>
            <select id="abonneId" name="abonneId" required>
                <option value="">- Selectionner -</option>
                <?php foreach ($utilisateurs as $u): ?>
                    <?php
                    $uid = (string)($u['id'] ?? '');
                    $label = trim((string)($u['prenom'] ?? '') . ' ' . (string)($u['nom'] ?? ''));
                    ?>
                    <option value="<?= h($uid) ?>" <?= $uid === (string)$abonneId ? 'selected' : '' ?>>
                        <?= h($label) ?> (#<?= h($uid) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form__row">
            <label for="dateLivraison">Date de livraison</label>
            <input id="dateLivraison" name="dateLivraison" type="date" value="<?= h((string)$dateLivraison) ?>" required />
        </div>

        <div class="form__row">
            <label for="adresseLivraison">Adresse de livraison</label>
            <input id="adresseLivraison" name="adresseLivraison" type="text" value="<?= h((string)$adresseLivraison) ?>" required />
        </div>

        <div class="form__row">
            <label>Lignes de commande</label>
            <p style="margin:0 0 10px 0; opacity:.8;">Ajoute 1 ou plusieurs lignes (menu + quantite).</p>

            <?php $existingCount = max(3, count((array)$selectedMenuIds), count((array)$quantites)); ?>
            <?php for ($i = 0; $i < $existingCount; $i++): ?>
                <?php
                $menuIdVal = isset($selectedMenuIds[$i]) ? (string)$selectedMenuIds[$i] : '';
                $qteVal = isset($quantites[$i]) ? (string)$quantites[$i] : '';
                ?>
                <div class="form__row" style="display:flex; gap:10px; align-items:end;">
                    <div style="flex:1;">
                        <label for="menuId_<?= $i ?>">Menu</label>
                        <select id="menuId_<?= $i ?>" name="menuId[]">
                            <option value="">-</option>
                            <?php foreach ($menus as $m): ?>
                                <?php
                                $mid = (string)($m['id'] ?? '');
                                $mnom = (string)($m['nom'] ?? ('Menu #' . $mid));
                                $mprix = $m['prixTotal'] ?? null;
                                $lbl = $mnom . ($mprix !== null ? ' - ' . $mprix . ' EUR' : '');
                                ?>
                                <option value="<?= h($mid) ?>" <?= $mid === $menuIdVal ? 'selected' : '' ?>>
                                    <?= h($lbl) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="width:140px;">
                        <label for="quantite_<?= $i ?>">Quantite</label>
                        <input id="quantite_<?= $i ?>" name="quantite[]" type="number" min="1" step="1" value="<?= h($qteVal) ?>" />
                    </div>
                </div>
            <?php endfor; ?>
        </div>

        <div class="form__actions">
            <button class="btn" type="submit">Creer la commande</button>
            <a class="btn btn--ghost" href="<?= h(route_url('/commandes')) ?>">Annuler</a>
        </div>
    </form>
</section>

