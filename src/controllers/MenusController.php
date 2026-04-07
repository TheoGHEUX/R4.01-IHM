<?php

declare(strict_types=1);

class MenusController
{
    private Renderer $renderer;
    private ListMenusUseCase $listMenusUseCase;
    private LoadMenuFormDataUseCase $loadMenuFormDataUseCase;
    private CreateMenuUseCase $createMenuUseCase;
    private GetMenuUseCase $getMenuUseCase;
    private UpdateMenuUseCase $updateMenuUseCase;
    private DeleteMenuUseCase $deleteMenuUseCase;

    public function __construct(
        Renderer $renderer,
        ListMenusUseCase $listMenusUseCase,
        LoadMenuFormDataUseCase $loadMenuFormDataUseCase,
        CreateMenuUseCase $createMenuUseCase,
        GetMenuUseCase $getMenuUseCase,
        UpdateMenuUseCase $updateMenuUseCase,
        DeleteMenuUseCase $deleteMenuUseCase
    ) {
        $this->renderer = $renderer;
        $this->listMenusUseCase = $listMenusUseCase;
        $this->loadMenuFormDataUseCase = $loadMenuFormDataUseCase;
        $this->createMenuUseCase = $createMenuUseCase;
        $this->getMenuUseCase = $getMenuUseCase;
        $this->updateMenuUseCase = $updateMenuUseCase;
        $this->deleteMenuUseCase = $deleteMenuUseCase;
    }

    public function index(Request $request): void
    {
        $result = $this->listMenusUseCase->execute();
        if (!$result['ok']) {
            $this->renderer->render('error', ['pageTitle' => 'Erreur', 'errors' => $result['errors']]);
            return;
        }

        $this->renderer->render('menus/index', [
            'pageTitle' => 'Menus',
            'menus' => $result['menus'],
        ]);
    }

    public function createForm(Request $request): void
    {
        $formData = $this->loadMenuFormDataUseCase->execute();
        if (!$formData['ok']) {
            $this->renderer->render('error', ['pageTitle' => 'Erreur', 'errors' => $formData['errors']]);
            return;
        }

        $this->renderer->render('menus/form', [
            'pageTitle' => 'Creer un menu',
            'mode' => 'create',
            'errors' => [],
            'menuId' => '',
            'nom' => '',
            'createurId' => '',
            'selectedPlatIds' => [],
            'plats' => $formData['plats'],
            'utilisateurs' => $formData['utilisateurs'],
        ]);
    }

    public function store(Request $request): void
    {
        $nom = trim((string)$request->post('nom', ''));
        $createurId = (string)$request->post('createurId', '');
        $selectedPlatIds = $request->postArray('platIds');

        $result = $this->createMenuUseCase->execute($nom, $createurId, $selectedPlatIds);
        if ($result['ok']) {
            Response::redirect('/menus');
        }

        $formData = $this->loadMenuFormDataUseCase->execute();
        $plats = $formData['plats'] ?? [];
        $utilisateurs = $formData['utilisateurs'] ?? [];

        $this->renderer->render('menus/form', [
            'pageTitle' => 'Creer un menu',
            'mode' => 'create',
            'errors' => $result['errors'] ?? ['Erreur inconnue.'],
            'menuId' => '',
            'nom' => $nom,
            'createurId' => $createurId,
            'selectedPlatIds' => $selectedPlatIds,
            'plats' => $plats,
            'utilisateurs' => $utilisateurs,
        ]);
    }

    public function editForm(Request $request, string $id): void
    {
        $menuRes = $this->getMenuUseCase->execute($id);
        $formData = $this->loadMenuFormDataUseCase->execute();

        if (!$menuRes['ok']) {
            $this->renderer->render('error', ['pageTitle' => 'Erreur', 'errors' => $menuRes['errors']]);
            return;
        }

        if (!$formData['ok']) {
            $this->renderer->render('error', ['pageTitle' => 'Erreur', 'errors' => $formData['errors']]);
            return;
        }

        $menu = $menuRes['menu'];
        $selectedPlatIds = [];
        if (isset($menu['plats']) && is_array($menu['plats'])) {
            foreach ($menu['plats'] as $plat) {
                if (is_array($plat) && isset($plat['id'])) {
                    $selectedPlatIds[] = (string)$plat['id'];
                }
            }
        }

        $this->renderer->render('menus/form', [
            'pageTitle' => 'Modifier un menu',
            'mode' => 'edit',
            'errors' => [],
            'menuId' => $id,
            'nom' => (string)($menu['nom'] ?? ''),
            'createurId' => (string)($menu['createurId'] ?? ''),
            'selectedPlatIds' => $selectedPlatIds,
            'plats' => $formData['plats'],
            'utilisateurs' => $formData['utilisateurs'],
        ]);
    }

    public function update(Request $request, string $id): void
    {
        $nom = trim((string)$request->post('nom', ''));
        $createurId = (string)$request->post('createurId', '');
        $selectedPlatIds = $request->postArray('platIds');

        $result = $this->updateMenuUseCase->execute($id, $nom, $createurId, $selectedPlatIds);
        if ($result['ok']) {
            Response::redirect('/menus');
        }

        $formData = $this->loadMenuFormDataUseCase->execute();
        $plats = $formData['plats'] ?? [];
        $utilisateurs = $formData['utilisateurs'] ?? [];

        $this->renderer->render('menus/form', [
            'pageTitle' => 'Modifier un menu',
            'mode' => 'edit',
            'errors' => $result['errors'] ?? ['Erreur inconnue.'],
            'menuId' => $id,
            'nom' => $nom,
            'createurId' => $createurId,
            'selectedPlatIds' => $selectedPlatIds,
            'plats' => $plats,
            'utilisateurs' => $utilisateurs,
        ]);
    }

    public function delete(Request $request, string $id): void
    {
        $result = $this->deleteMenuUseCase->execute($id);
        if ($result['ok']) {
            Response::redirect('/menus');
        }

        $this->renderer->render('error', [
            'pageTitle' => 'Erreur',
            'errors' => $result['errors'] ?? ['Erreur inconnue lors de la suppression.'],
        ]);
    }
}

