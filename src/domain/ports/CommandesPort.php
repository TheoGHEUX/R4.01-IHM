<?php

declare(strict_types=1);

interface CommandesPort
{
    public function listCommandes(): array;

    public function createCommande(array $payload): array;
}

