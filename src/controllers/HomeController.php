<?php

declare(strict_types=1);

/**
 * Contrôleur de la page d'accueil.
 *
 * Responsabilité :
 * - Afficher la page d’accueil de l’IHM (menu de navigation).
 *
 * Dépendances :
 * - {@see Renderer} : moteur de rendu de vues (presentation/gui).
 */
class HomeController
{
    private Renderer $renderer;

    /**
     * @param Renderer $renderer Service de rendu des vues.
     */
    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Affiche la page d'accueil.
     *
     * @param Request $request Requête HTTP courante.
     * @return void
     */
    public function index(Request $request): void
    {
        $this->renderer->render('home', [
            'pageTitle' => 'IHM - Livraison de repas',
        ]);
    }
}