<?php

declare(strict_types=1);

class MenusGateway
{
    private ApiClient $client;
    private string $baseUrl;
    private int $timeout;

    public function __construct(ApiClient $client, string $baseUrl, int $timeout)
    {
        $this->client = $client;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
    }

    public function listMenus(): array
    {
        return $this->client->get($this->baseUrl . '/menus', $this->timeout);
    }

    public function getMenu(string $id): array
    {
        return $this->client->get($this->baseUrl . '/menus/' . rawurlencode($id), $this->timeout);
    }

    public function createMenu(array $payload): array
    {
        return $this->client->post($this->baseUrl . '/menus', $payload, $this->timeout);
    }

    public function updateMenu(string $id, array $payload): array
    {
        return $this->client->put($this->baseUrl . '/menus/' . rawurlencode($id), $payload, $this->timeout);
    }

    public function deleteMenu(string $id): array
    {
        return $this->client->delete($this->baseUrl . '/menus/' . rawurlencode($id), $this->timeout);
    }
}

