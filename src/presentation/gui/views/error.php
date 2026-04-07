<section class="card">
    <h1>Erreur</h1>
    <div class="alert alert--error">
        <ul>
            <?php foreach (($errors ?? ['Une erreur est survenue.']) as $error): ?>
                <li><?= h((string)$error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

