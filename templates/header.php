<?php
$pageTitle = $pageTitle ?? "IHM – Livraison de repas";
$logoPath  = $logoPath  ?? "/assets/logo.png"; // mets ton image ici

$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
$isHome = ($currentScript === 'index.php');
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/main.css" />
</head>
<body>
<header class="site-header">
    <div class="site-header__inner">
        <a class="site-brand" href="/index.php">
            <img
                    class="site-brand__logo"
                    src="<?= htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8') ?>"
                    alt="Logo"
            />
            <span class="site-brand__title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></span>
        </a>

        <?php if (!$isHome): ?>
            <a class="btn btn--ghost site-header__back" href="/index.php">← Retour</a>
        <?php endif; ?>
    </div>
</header>

<main class="site-main">