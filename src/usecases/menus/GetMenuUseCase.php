<?php

declare(strict_types=1);

class GetMenuUseCase
{
    private MenusInterface $menusGateway;

    public function __construct(MenusInterface $menusGateway)
    {
        $this->menusGateway = $menusGateway;
    }

    public function execute(string $id): array
    {
        if ($id === '') {
            return ['ok' => false, 'errors' => ['Parametre id manquant.']];
        }

        $res = $this->menusGateway->getMenu($id);
        if (!$res['ok']) {
            return ['ok' => false, 'errors' => ['Erreur API (menu): ' . (string)$res['error']]];
        }

        if (($res['http_code'] ?? 500) < 200 || ($res['http_code'] ?? 500) >= 300) {
            return ['ok' => false, 'errors' => ['Menu introuvable (HTTP ' . (int)$res['http_code'] . ').']];
        }

        $menu = $res['data'] ?? null;
        if (!is_array($menu)) {
            return ['ok' => false, 'errors' => ['Reponse JSON invalide (menu).']];
        }

        return ['ok' => true, 'menu' => $menu];
    }
}