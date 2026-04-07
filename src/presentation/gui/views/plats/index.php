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
        <?php foreach ($plats as $plat): ?>
            <tr>
                <td><?= h((string)($plat['nom'] ?? $plat['name'] ?? '-')) ?></td>
                <td><?= h((string)($plat['description'] ?? '-')) ?></td>
                <td>
                    <?php $prix = $plat['prix'] ?? $plat['price'] ?? null; ?>
                    <?= $prix === null ? '-' : h((string)$prix) . ' EUR' ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

