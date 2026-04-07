<?php
$pageTitle = $pageTitle ?? "IHM – Livraison de repas";
$logoPath  = $logoPath  ?? "/assets/logo.png"; // mets ton image ici

$route = $_GET['route'] ?? null;
$currentPath = is_string($route) && $route !== '' ? rawurldecode($route) : (string)(parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
$isHome = ($currentPath === '/' || $currentPath === '/index.php');
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
        <div class="site-brand" href="<?= htmlspecialchars(route_url('/'), ENT_QUOTES, 'UTF-8') ?>">
            <img
                    class="site-brand__logo"
                    src="<?= htmlspecialchars($logoPath, ENT_QUOTES, 'UTF-8') ?>"
                    alt="Logo"
            />
            <span class="site-brand__title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <?php if (!$isHome): ?>
            <a class="btn btn--ghost site-header__back" href="<?= htmlspecialchars(route_url('/'), ENT_QUOTES, 'UTF-8') ?>">← Retour</a>
        <?php endif; ?>
    </div>
</header>

<main class="site-main">