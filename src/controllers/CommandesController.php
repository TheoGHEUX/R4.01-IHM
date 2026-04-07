<?php

declare(strict_types=1);

class CommandesController
{
    private Renderer $renderer;
    private ListCommandesUseCase $listCommandesUseCase;
    private LoadCommandeFormDataUseCase $loadCommandeFormDataUseCase;
    private CreateCommandeUseCase $createCommandeUseCase;

    public function __construct(
        Renderer $renderer,
        ListCommandesUseCase $listCommandesUseCase,
        LoadCommandeFormDataUseCase $loadCommandeFormDataUseCase,
        CreateCommandeUseCase $createCommandeUseCase
    ) {
        $this->renderer = $renderer;
        $this->listCommandesUseCase = $listCommandesUseCase;
        $this->loadCommandeFormDataUseCase = $loadCommandeFormDataUseCase;
        $this->createCommandeUseCase = $createCommandeUseCase;
    }

    public function index(Request $request): void
    {
        $result = $this->listCommandesUseCase->execute();
        if (!$result['ok']) {
            $this->renderer->render('error', ['pageTitle' => 'Erreur', 'errors' => $result['errors']]);
            return;
        }

        $this->renderer->render('commandes/index', [
            'pageTitle' => 'Commandes',
            'commandes' => $result['commandes'],
        ]);
    }

    public function createForm(Request $request): void
    {
        $formData = $this->loadCommandeFormDataUseCase->execute();
        if (!$formData['ok']) {
            $this->renderer->render('error', ['pageTitle' => 'Erreur', 'errors' => $formData['errors']]);
            return;
        }

        $this->renderer->render('commandes/form', [
            'pageTitle' => 'Creer une commande',
            'errors' => [],
            'abonneId' => '',
            'adresseLivraison' => '',
            'dateLivraison' => today(),
            'selectedMenuIds' => [],
            'quantites' => [],
            'utilisateurs' => $formData['utilisateurs'],
            'menus' => $formData['menus'],
        ]);
    }

    public function store(Request $request): void
    {
        $abonneId = (string)$request->post('abonneId', '');
        $adresseLivraison = trim((string)$request->post('adresseLivraison', ''));
        $dateLivraison = trim((string)$request->post('dateLivraison', today()));
        $selectedMenuIds = $request->postArray('menuId');
        $quantites = $request->postArray('quantite');

        $result = $this->createCommandeUseCase->execute(
            $abonneId,
            $adresseLivraison,
            $dateLivraison,
            $selectedMenuIds,
            $quantites
        );

        if ($result['ok']) {
            Response::redirect('/commandes');
        }

        $formData = $this->loadCommandeFormDataUseCase->execute();
        $utilisateurs = $formData['utilisateurs'] ?? [];
        $menus = $formData['menus'] ?? [];

        $this->renderer->render('commandes/form', [
            'pageTitle' => 'Creer une commande',
            'errors' => $result['errors'] ?? ['Erreur inconnue.'],
            'abonneId' => $abonneId,
            'adresseLivraison' => $adresseLivraison,
            'dateLivraison' => $dateLivraison,
            'selectedMenuIds' => $selectedMenuIds,
            'quantites' => $quantites,
            'utilisateurs' => $utilisateurs,
            'menus' => $menus,
        ]);
    }
}

