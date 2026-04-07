<?php

declare(strict_types=1);

class ListCommandesUseCase
{
    private CommandesGateway $commandesGateway;

    public function __construct(CommandesGateway $commandesGateway)
    {
        $this->commandesGateway = $commandesGateway;
    }

    public function execute(): array
    {
        $res = $this->commandesGateway->listCommandes();
        if (!$res['ok']) {
            return ['ok' => false, 'errors' => ['Erreur API: ' . (string)$res['error']]];
        }

        $commandes = CommandesDomain::normalizeCollection($res['data'] ?? null);
        if (!is_array($commandes)) {
            return ['ok' => false, 'errors' => ['Format inattendu: commandes introuvables.']];
        }

        return ['ok' => true, 'commandes' => $commandes];
    }
}

