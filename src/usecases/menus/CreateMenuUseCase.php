<?php

declare(strict_types=1);

class CreateMenuUseCase
{
    private MenusGateway $menusGateway;
    private LoadMenuFormDataUseCase $loadMenuFormDataUseCase;

    public function __construct(MenusGateway $menusGateway, LoadMenuFormDataUseCase $loadMenuFormDataUseCase)
    {
        $this->menusGateway = $menusGateway;
        $this->loadMenuFormDataUseCase = $loadMenuFormDataUseCase;
    }

    public function execute(string $nom, string $createurId, array $selectedPlatIds): array
    {
        $errors = MenusDomain::validateInput($nom, $createurId, $selectedPlatIds);
        $formData = $this->loadMenuFormDataUseCase->execute();

        if (!$formData['ok']) {
            return ['ok' => false, 'errors' => $formData['errors']];
        }

        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        $plats = $formData['plats'];
        $utilisateurs = $formData['utilisateurs'];

        [$platsSelectionnes, $prixTotal] = MenusDomain::buildSelectedPlatsAndTotal($plats, $selectedPlatIds);
        $createurNom = MenusDomain::findUtilisateurNom($utilisateurs, $createurId);

        if ($createurNom === null) {
            return ['ok' => false, 'errors' => ['Createur introuvable.']];
        }

        $payload = [
            'nom' => $nom,
            'createurId' => (int)$createurId,
            'createurNom' => $createurNom,
            'dateCreation' => today(),
            'dateMiseAJour' => today(),
            'plats' => $platsSelectionnes,
            'prixTotal' => $prixTotal,
        ];

        $res = $this->menusGateway->createMenu($payload);
        if (!$res['ok']) {
            return ['ok' => false, 'errors' => ['Erreur reseau lors de la creation du menu: ' . (string)$res['error']]];
        }

        if (($res['http_code'] ?? 500) < 200 || ($res['http_code'] ?? 500) >= 300) {
            return ['ok' => false, 'errors' => ['Erreur HTTP ' . (int)$res['http_code'] . ' lors de la creation du menu.']];
        }

        return ['ok' => true];
    }
}

