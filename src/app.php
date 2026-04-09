<?php

declare(strict_types=1);

/**
 * Composition Root / Point d'assemblage de l'application.
 *
 * Ce fichier :
 * 1) Charge le bootstrap (chargement classes + helpers)
 * 2) Charge la configuration {@see src/config.php}
 * 3) Instancie l'infrastructure (ApiClient + Gateways)
 * 4) Instancie les UseCases (couche applicative)
 * 5) Instancie le Renderer et les Controllers (couche présentation)
 * 6) Déclare les routes (Router)
 * 7) Construit la Request et déclenche le dispatch
 *
 * Ce fichier est inclus par {@see public/index.php}.
 *
 * @see public/index.php
 * @see src/bootstrap.php
 * @see src/config.php
 */

require_once __DIR__ . '/bootstrap.php';

$config = require __DIR__ . '/config.php';
$timeout = (int)($config['http']['timeout'] ?? 5);

// Infrastructure (HTTP client + gateways)
$apiClient = new ApiClient();
$platsGateway = new PlatsGateway($apiClient, (string)$config['services']['plats-utilisateurs'], $timeout);
$menusGateway = new MenusGateway($apiClient, (string)$config['services']['menus'], $timeout);
$commandesGateway = new CommandesGateway($apiClient, (string)$config['services']['commandes'], $timeout);

// UseCases
$listPlatsUseCase = new ListPlatsUseCase($platsGateway);

$listMenusUseCase = new ListMenusUseCase($menusGateway);
$getMenuUseCase = new GetMenuUseCase($menusGateway);
$loadMenuFormDataUseCase = new LoadMenuFormDataUseCase($platsGateway);
$createMenuUseCase = new CreateMenuUseCase($menusGateway, $loadMenuFormDataUseCase);
$updateMenuUseCase = new UpdateMenuUseCase($menusGateway, $loadMenuFormDataUseCase, $getMenuUseCase);
$deleteMenuUseCase = new DeleteMenuUseCase($menusGateway);

$listCommandesUseCase = new ListCommandesUseCase($commandesGateway);
$loadCommandeFormDataUseCase = new LoadCommandeFormDataUseCase($platsGateway, $menusGateway);
$createCommandeUseCase = new CreateCommandeUseCase($commandesGateway, $loadCommandeFormDataUseCase);

// Presentation (renderer + controllers)
$renderer = new Renderer(__DIR__ . '/presentation/gui/layout', __DIR__ . '/presentation/gui/views');

$homeController = new HomeController($renderer);
$platsController = new PlatsController($renderer, $listPlatsUseCase);

$menusController = new MenusController(
    $renderer,
    $listMenusUseCase,
    $loadMenuFormDataUseCase,
    $createMenuUseCase,
    $getMenuUseCase,
    $updateMenuUseCase,
    $deleteMenuUseCase
);

$commandesController = new CommandesController(
    $renderer,
    $listCommandesUseCase,
    $loadCommandeFormDataUseCase,
    $createCommandeUseCase
);

// Routing
$router = new Router();

$router->get('/', [$homeController, 'index']);
$router->get('/plats', [$platsController, 'index']);

$router->get('/menus', [$menusController, 'index']);
$router->get('/menus/create', [$menusController, 'createForm']);
$router->post('/menus', [$menusController, 'store']);
$router->get('/menus/{id}/edit', [$menusController, 'editForm']);
$router->post('/menus/{id}/update', [$menusController, 'update']);
$router->post('/menus/{id}/delete', [$menusController, 'delete']);

$router->get('/commandes', [$commandesController, 'index']);
$router->get('/commandes/create', [$commandesController, 'createForm']);
$router->post('/commandes', [$commandesController, 'store']);

// Dispatch
$request = Request::fromGlobals();
$router->dispatch($request);