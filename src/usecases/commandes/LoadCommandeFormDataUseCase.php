<?php

declare(strict_types=1);

class LoadCommandeFormDataUseCase
{
    private PlatsPort $platsGateway;
    private MenusPort $menusGateway;

    public function __construct(PlatsPort $platsGateway, MenusPort $menusGateway)
    {
        $this->platsGateway = $platsGateway;
        $this->menusGateway = $menusGateway;
    }

    public function execute(): array
    {
        $usersRes = $this->platsGateway->listUtilisateurs();
        $menusRes = $this->menusGateway->listMenus();

        if (!$usersRes['ok'] || !$menusRes['ok']) {
            return ['ok' => false, 'errors' => ['Impossible de charger les donnees necessaires.']];
        }

        $utilisateurs = $usersRes['data'] ?? null;
        $menus = MenusDomain::normalizeCollection($menusRes['data'] ?? null);

        if (!is_array($utilisateurs) || !is_array($menus)) {
            return ['ok' => false, 'errors' => ['Reponse JSON invalide depuis un service.']];
        }

        return ['ok' => true, 'utilisateurs' => $utilisateurs, 'menus' => $menus];
    }
}