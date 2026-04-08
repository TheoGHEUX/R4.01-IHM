<?php

declare(strict_types=1);

/**
 * Contrôleur des plats.
 *
 * Responsabilités :
 * - Orchestrer l'affichage de la liste des plats.
 * - Gérer l'affichage d'une page d'erreur si le Usecase échoue.
 *
 * Dépendances :
 * - {@see Renderer} : rendu HTML des vues.
 * - {@see ListPlatsUseCase} : Usecase applicatif (récupération/normalisation des plats).
 */
class PlatsController
{
    private Renderer $renderer;
    private ListPlatsUseCase $listPlatsUseCase;

    /**
     * @param Renderer $renderer Service de rendu.
     * @param ListPlatsUseCase $listPlatsUseCase Usecase pour lister les plats.
     */
    public function __construct(Renderer $renderer, ListPlatsUseCase $listPlatsUseCase)
    {
        $this->renderer = $renderer;
        $this->listPlatsUseCase = $listPlatsUseCase;
    }

    /**
     * Affiche la liste des plats.
     *
     * Comportement :
     * - appelle {@see ListPlatsUseCase::execute()}
     * - si erreur : rend la vue "error"
     * - sinon : rend la vue "plats/index"
     *
     * @param Request $request Requête HTTP.
     * @return void
     */
    public function index(Request $request): void
    {
        $result = $this->listPlatsUseCase->execute();
        if (!$result['ok']) {
            $this->renderer->render('error', [
                'pageTitle' => 'Erreur',
                'errors' => $result['errors'],
            ]);
            return;
        }

        $this->renderer->render('plats/index', [
            'pageTitle' => 'Plats',
            'plats' => $result['plats'],
        ]);
    }
}