<?php

declare(strict_types=1);

class LoadMenuFormDataUseCase
{
    private PlatsInterface $platsGateway;

    public function __construct(PlatsInterface $platsGateway)
    {
        $this->platsGateway = $platsGateway;
    }

    public function execute(): array
    {
        $platsRes = $this->platsGateway->listPlats();
        $usersRes = $this->platsGateway->listUtilisateurs();

        if (!$platsRes['ok'] || !$usersRes['ok']) {
            return ['ok' => false, 'errors' => ['Impossible de charger les donnees necessaires.']];
        }

        $plats = PlatsDomain::normalizeCollection($platsRes['data'] ?? null);
        $utilisateurs = $usersRes['data'] ?? null;

        if (!is_array($plats) || !is_array($utilisateurs)) {
            return ['ok' => false, 'errors' => ['Reponse JSON invalide depuis un service.']];
        }

        return ['ok' => true, 'plats' => $plats, 'utilisateurs' => $utilisateurs];
    }
}