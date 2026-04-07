<?php

declare(strict_types=1);

class ListMenusUseCase
{
    private MenusGateway $menusGateway;

    public function __construct(MenusGateway $menusGateway)
    {
        $this->menusGateway = $menusGateway;
    }

    public function execute(): array
    {
        $res = $this->menusGateway->listMenus();
        if (!$res['ok']) {
            return ['ok' => false, 'errors' => ['Erreur API: ' . (string)$res['error']]];
        }

        $menus = MenusDomain::normalizeCollection($res['data'] ?? null);
        if (!is_array($menus)) {
            return ['ok' => false, 'errors' => ['Format inattendu: menus introuvables.']];
        }

        return ['ok' => true, 'menus' => $menus];
    }
}

