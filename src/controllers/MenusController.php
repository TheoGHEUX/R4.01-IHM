<?php

declare(strict_types=1);

/**
 * Contrôleur des menus.
 *
 * Responsabilités :
 * - Afficher la liste des menus.
 * - Afficher le formulaire de création et traiter sa soumission.
 * - Afficher le formulaire d'édition et traiter sa soumission.
 * - Supprimer un menu.
 *
 * Le contrôleur ne contient pas de logique métier :
 * - il délègue la validation et la construction des payloads aux UseCases/Domains.
 * - il choisit uniquement quelle vue rendre et quand rediriger.
 *
 * Dépendances :
 * - {@see Renderer}
 * - {@see ListMenusUseCase}
 * - {@see LoadMenuFormDataUseCase}
 * - {@see CreateMenuUseCase}
 * - {@see GetMenuUseCase}
 * - {@see UpdateMenuUseCase}
 * - {@see DeleteMenuUseCase}
 */
class MenusController
{
    private Renderer $renderer;
    private ListMenusUseCase $listMenusUseCase;
    private LoadMenuFormDataUseCase $loadMenuFormDataUseCase;
    private CreateMenuUseCase $createMenuUseCase;
    private GetMenuUseCase $getMenuUseCase;
    private UpdateMenuUseCase $updateMenuUseCase;
    private DeleteMenuUseCase $deleteMenuUseCase;

    /**
     * @param Renderer $renderer Service de rendu.
     * @param ListMenusUseCase $listMenusUseCase Usecase de listing.
     * @param LoadMenuFormDataUseCase $loadMenuFormDataUseCase Usecase de chargement des données de formulaire (plats/utilisateurs).
     * @param CreateMenuUseCase $createMenuUseCase Usecase de création.
     * @param GetMenuUseCase $getMenuUseCase Usecase de récupération d'un menu.
     * @param UpdateMenuUseCase $updateMenuUseCase Usecase de mise à jour.
     * @param DeleteMenuUseCase $deleteMenuUseCase Usecase de suppression.
     */
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

    /**
     * Affiche la liste des menus.
     *
     * @param Request $request Requête HTTP.
     * @return void
     */
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

    /**
     * Affiche le formulaire de création d'un menu.
     *
     * @param Request $request Requête HTTP.
     * @return void
     */
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

    /**
     * Traite la soumission du formulaire de création.
     *
     * Comportement :
     * - en cas de succès : redirection vers /menus
     * - sinon : ré-affiche le formulaire avec erreurs et valeurs "sticky"
     *
     * @param Request $request Requête HTTP.
     * @return void
     */
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

    /**
     * Affiche le formulaire d'édition d'un menu existant.
     *
     * @param Request $request Requête HTTP.
     * @param string $id Identifiant du menu.
     * @return void
     */
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

    /**
     * Traite la soumission du formulaire d'édition.
     *
     * @param Request $request Requête HTTP.
     * @param string $id Identifiant du menu.
     * @return void
     */
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

    /**
     * Supprime un menu.
     *
     * @param Request $request Requête HTTP.
     * @param string $id Identifiant du menu.
     * @return void
     */
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