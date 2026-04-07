<?php

declare(strict_types=1);

class HomeController
{
    private Renderer $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function index(Request $request): void
    {
        $this->renderer->render('home', [
            'pageTitle' => 'IHM - Livraison de repas',
        ]);
    }
}

