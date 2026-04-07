<?php

declare(strict_types=1);

class CommandesGateway
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

    public function listCommandes(): array
    {
        return $this->client->get($this->baseUrl . '/commandes', $this->timeout);
    }

    public function createCommande(array $payload): array
    {
        return $this->client->post($this->baseUrl . '/commandes', $payload, $this->timeout);
    }
}

