<?php

declare(strict_types=1);

interface CommandesInterface
{
    public function listCommandes(): array;

    public function createCommande(array $payload): array;
}

