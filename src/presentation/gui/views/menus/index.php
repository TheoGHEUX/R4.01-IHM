<a class="btn" href="<?= h(route_url('/menus/create')) ?>">+ Creer un menu</a>

<section class="card">
    <h1>Menus</h1>

    <table class="table">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Createur</th>
            <th>Date creation</th>
            <th>Derniere MAJ</th>
            <th>Plats</th>
            <th>Total</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($menus as $menu): ?>
            <tr>
                <td><?= h((string)($menu['nom'] ?? '-')) ?></td>
                <td>
                    <?php
                    $createurNom = $menu['createurNom'] ?? '-';
                    $createurId = $menu['createurId'] ?? null;
                    $txt = (string)$createurNom;
                    if ($createurId !== null) {
                        $txt .= ' (#' . $createurId . ')';
                    }
                    echo h($txt);
                    ?>
                </td>
                <td><?= h((string)($menu['dateCreation'] ?? '-')) ?></td>
                <td><?= h((string)($menu['dateMiseAJour'] ?? '-')) ?></td>
                <td><?= format_plats($menu['plats'] ?? []) ?></td>
                <td>
                    <?php $total = $menu['prixTotal'] ?? null; ?>
                    <?= $total === null ? '-' : h((string)$total) . ' EUR' ?>
                </td>
                <td>
                    <?php $id = (string)($menu['id'] ?? ''); ?>
                    <?php if ($id !== ''): ?>
                        <a class="btn btn--ghost" href="<?= h(route_url('/menus/' . $id . '/edit')) ?>">Modifier</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

