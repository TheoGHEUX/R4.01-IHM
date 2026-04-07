<?php

declare(strict_types=1);

class PlatsController
{
    private Renderer $renderer;
    private ListPlatsUseCase $listPlatsUseCase;

    public function __construct(Renderer $renderer, ListPlatsUseCase $listPlatsUseCase)
    {
        $this->renderer = $renderer;
        $this->listPlatsUseCase = $listPlatsUseCase;
    }

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

