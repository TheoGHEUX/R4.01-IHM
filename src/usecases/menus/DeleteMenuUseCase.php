<?php

declare(strict_types=1);

class DeleteMenuUseCase
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

        $res = $this->menusGateway->deleteMenu($id);
        if (!$res['ok']) {
            return ['ok' => false, 'errors' => ['Erreur reseau lors de la suppression: ' . (string)$res['error']]];
        }

        if (($res['http_code'] ?? 500) < 200 || ($res['http_code'] ?? 500) >= 300) {
            return ['ok' => false, 'errors' => ['Erreur HTTP ' . (int)$res['http_code'] . ' lors de la suppression.']];
        }

        return ['ok' => true];
    }
}