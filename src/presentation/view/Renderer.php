<?php

declare(strict_types=1);

/**
 * Service de rendu de vues.
 *
 * Rôle :
 * - Charger un layout commun (header + footer)
 * - Charger une vue métier (ex: "menus/index", "commandes/form", ...)
 *
 */
class Renderer
{
    private string $templatesDir;
    private string $viewsDir;

    /**
     * @param string $templatesDir Répertoire des templates de layout (header/footer).
     * @param string $viewsDir Répertoire racine des vues.
     */
    public function __construct(string $templatesDir, string $viewsDir)
    {
        $this->templatesDir = $templatesDir;
        $this->viewsDir = $viewsDir;
    }

    /**
     * Rend une vue dans le layout.
     *
     * Paramètres usuels :
     * - pageTitle (string) : titre de la page (valeur par défaut si absent)
     *
     * @param string $view Nom logique de la vue (ex: "menus/index").
     * @param array $params Variables exposées à la vue.
     * @return void
     */
    public function render(string $view, array $params = []): void
    {
        // Variables "globales" pour le layout
        $pageTitle = $params['pageTitle'] ?? 'IHM - Livraison de repas';
        $logoPath = '/assets/logo.png';

        // Variables de la vue (expose $menus, $plats, $errors, etc.)
        extract($params, EXTR_SKIP);

        require $this->templatesDir . '/header.php';
        require $this->viewsDir . '/' . $view . '.php';
        require $this->templatesDir . '/footer.php';
    }
}