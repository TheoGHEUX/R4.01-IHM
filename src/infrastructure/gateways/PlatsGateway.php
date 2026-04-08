<?php

declare(strict_types=1);

/**
 * Gateway HTTP pour le microservice "plats-utilisateurs".
 *
 */
class PlatsGateway implements PlatsInterface
{
    private ApiClient $client;
    private string $baseUrl;
    private int $timeout;

    /**
     * @param ApiClient $client Client HTTP bas niveau.
     * @param string $baseUrl Base URL du microservice (ex: http://localhost:3003).
     * @param int $timeout Timeout (secondes).
     */
    public function __construct(ApiClient $client, string $baseUrl, int $timeout)
    {
        $this->client = $client;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
    }

    /**
     * @inheritDoc
     */
    public function listPlats(): array
    {
        return $this->client->get($this->baseUrl . '/plats', $this->timeout);
    }

    /**
     * @inheritDoc
     */
    public function listUtilisateurs(): array
    {
        return $this->client->get($this->baseUrl . '/utilisateurs', $this->timeout);
    }
}