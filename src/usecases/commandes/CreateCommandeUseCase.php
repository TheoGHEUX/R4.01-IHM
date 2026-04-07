<?php

declare(strict_types=1);

class CreateCommandeUseCase
{
    private CommandesGateway $commandesGateway;
    private LoadCommandeFormDataUseCase $loadCommandeFormDataUseCase;

    public function __construct(CommandesGateway $commandesGateway, LoadCommandeFormDataUseCase $loadCommandeFormDataUseCase)
    {
        $this->commandesGateway = $commandesGateway;
        $this->loadCommandeFormDataUseCase = $loadCommandeFormDataUseCase;
    }

    public function execute(
        string $abonneId,
        string $adresseLivraison,
        string $dateLivraison,
        array $selectedMenuIds,
        array $quantites
    ): array {
        $errors = CommandesDomain::validateInput($abonneId, $adresseLivraison, $dateLivraison);
        $formData = $this->loadCommandeFormDataUseCase->execute();

        if (!$formData['ok']) {
            return ['ok' => false, 'errors' => $formData['errors']];
        }

        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        $menusById = CommandesDomain::indexMenusById($formData['menus']);
        [$lignes, $prixTotal, $ligneErrors] = CommandesDomain::buildLignesAndTotal($menusById, $selectedMenuIds, $quantites);

        if ($ligneErrors) {
            return ['ok' => false, 'errors' => $ligneErrors];
        }

        $payload = [
            'abonneId' => (int)$abonneId,
            'dateCommande' => today(),
            'dateLivraison' => $dateLivraison,
            'adresseLivraison' => $adresseLivraison,
            'lignes' => $lignes,
            'prixTotal' => $prixTotal,
        ];

        $res = $this->commandesGateway->createCommande($payload);
        if (!$res['ok']) {
            return ['ok' => false, 'errors' => ['Erreur reseau lors de la creation de la commande: ' . (string)$res['error']]];
        }

        if (($res['http_code'] ?? 500) < 200 || ($res['http_code'] ?? 500) >= 300) {
            return ['ok' => false, 'errors' => ['Erreur HTTP ' . (int)$res['http_code'] . ' lors de la creation de la commande.']];
        }

        return ['ok' => true];
    }
}

