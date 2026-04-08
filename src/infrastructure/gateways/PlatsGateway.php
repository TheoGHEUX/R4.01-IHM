<?php

declare(strict_types=1);

class PlatsGateway implements PlatsPort
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

    public function listPlats(): array
    {
        return $this->client->get($this->baseUrl . '/plats', $this->timeout);
    }

    public function listUtilisateurs(): array
    {
        return $this->client->get($this->baseUrl . '/utilisateurs', $this->timeout);
    }
}