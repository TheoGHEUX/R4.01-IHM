<?php

declare(strict_types=1);

class Renderer
{
    private string $templatesDir;
    private string $viewsDir;

    public function __construct(string $templatesDir, string $viewsDir)
    {
        $this->templatesDir = $templatesDir;
        $this->viewsDir = $viewsDir;
    }

    public function render(string $view, array $params = []): void
    {
        $pageTitle = $params['pageTitle'] ?? 'IHM - Livraison de repas';
        $logoPath = '/assets/logo.png';

        extract($params, EXTR_SKIP);

        require $this->templatesDir . '/header.php';
        require $this->viewsDir . '/' . $view . '.php';
        require $this->templatesDir . '/footer.php';
    }
}

