<?php

declare(strict_types=1);

class UpdateMenuUseCase
{
    private MenusGateway $menusGateway;
    private LoadMenuFormDataUseCase $loadMenuFormDataUseCase;
    private GetMenuUseCase $getMenuUseCase;

    public function __construct(
        MenusGateway $menusGateway,
        LoadMenuFormDataUseCase $loadMenuFormDataUseCase,
        GetMenuUseCase $getMenuUseCase
    ) {
        $this->menusGateway = $menusGateway;
        $this->loadMenuFormDataUseCase = $loadMenuFormDataUseCase;
        $this->getMenuUseCase = $getMenuUseCase;
    }

    public function execute(string $id, string $nom, string $createurId, array $selectedPlatIds): array
    {
        $errors = MenusDomain::validateInput($nom, $createurId, $selectedPlatIds);
        $formData = $this->loadMenuFormDataUseCase->execute();
        $menuRes = $this->getMenuUseCase->execute($id);

        if (!$formData['ok']) {
            return ['ok' => false, 'errors' => $formData['errors']];
        }

        if (!$menuRes['ok']) {
            return ['ok' => false, 'errors' => $menuRes['errors']];
        }

        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }

        $menu = $menuRes['menu'];
        $plats = $formData['plats'];
        $utilisateurs = $formData['utilisateurs'];

        [$platsSelectionnes, $prixTotal] = MenusDomain::buildSelectedPlatsAndTotal($plats, $selectedPlatIds);
        $createurNom = MenusDomain::findUtilisateurNom($utilisateurs, $createurId);
        if ($createurNom === null) {
            return ['ok' => false, 'errors' => ['Createur introuvable.']];
        }

        $payload = [
            'id' => $menu['id'] ?? $id,
            'nom' => $nom,
            'createurId' => (int)$createurId,
            'createurNom' => $createurNom,
            'dateCreation' => $menu['dateCreation'] ?? today(),
            'dateMiseAJour' => today(),
            'plats' => $platsSelectionnes,
            'prixTotal' => $prixTotal,
        ];

        $res = $this->menusGateway->updateMenu($id, $payload);
        if (!$res['ok']) {
            return ['ok' => false, 'errors' => ['Erreur reseau lors de la modification: ' . (string)$res['error']]];
        }

        if (($res['http_code'] ?? 500) < 200 || ($res['http_code'] ?? 500) >= 300) {
            return ['ok' => false, 'errors' => ['Erreur HTTP ' . (int)$res['http_code'] . ' lors de la modification.']];
        }

        return ['ok' => true];
    }
}

