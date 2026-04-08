<?php

declare(strict_types=1);

class ListPlatsUseCase
{
    private PlatsInterface $platsGateway;

    public function __construct(PlatsInterface $platsGateway)
    {
        $this->platsGateway = $platsGateway;
    }

    public function execute(): array
    {
        $res = $this->platsGateway->listPlats();
        if (!$res['ok']) {
            return ['ok' => false, 'errors' => ['Erreur API: ' . (string)$res['error']]];
        }

        $plats = PlatsDomain::normalizeCollection($res['data'] ?? null);
        if (!is_array($plats)) {
            return ['ok' => false, 'errors' => ['Reponse JSON invalide pour les plats.']];
        }

        return ['ok' => true, 'plats' => $plats];
    }
}