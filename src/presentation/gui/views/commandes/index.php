<a class="btn" href="<?= h(route_url('/commandes/create')) ?>">+ Creer une commande</a>

<section class="card">
    <h1>Commandes</h1>

    <table class="table">
        <thead>
        <tr>
            <th>Abonne</th>
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
                <td><?= h((string)($cmd['abonneId'] ?? '-')) ?></td>
                <td><?= h((string)($cmd['dateCommande'] ?? '-')) ?></td>
                <td><?= h((string)($cmd['dateLivraison'] ?? '-')) ?></td>
                <td><?= h((string)($cmd['adresseLivraison'] ?? '-')) ?></td>
                <td><?= format_lignes($cmd['lignes'] ?? []) ?></td>
                <td>
                    <?php $total = $cmd['prixTotal'] ?? null; ?>
                    <?= $total === null ? '-' : h((string)$total) . ' EUR' ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

