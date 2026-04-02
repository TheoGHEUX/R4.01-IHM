<?php
$pageTitle = "IHM – Livraison de repas";
$logoPath = "/assets/logo.png";
require __DIR__ . '/../templates/header.php';
?>

    <section class="card">
        <h1>Accueil</h1>
        <p>Choisis une ressource à consulter :</p>

        <div class="actions">
            <a class="btn" href="/plats.php">Consulter les plats</a>
            <a class="btn" href="/menus.php">Consulter les menus</a>
            <a class="btn" href="/commandes.php">Consulter les commandes</a>
        </div>
    </section>

<?php require __DIR__ . '/../templates/footer.php'; ?>