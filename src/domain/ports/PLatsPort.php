<?php

declare(strict_types=1);

interface PlatsPort
{
    public function listPlats(): array;

    public function listUtilisateurs(): array;
}