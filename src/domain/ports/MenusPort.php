<?php

declare(strict_types=1);

interface MenusPort
{
    public function listMenus(): array;

    public function getMenu(string $id): array;

    public function createMenu(array $payload): array;

    public function updateMenu(string $id, array $payload): array;

    public function deleteMenu(string $id): array;
}