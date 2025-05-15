<?php

declare(strict_types=1);

namespace RobertWesner\SimpleMvcPhp\Tests\Route\Class\Service;

final class UserService
{
    public function findAll(): array
    {
        return [];
    }

    public function findOneBy(int $id): array
    {
        return ['name' => 'foo'];
    }

    public function create(string $username): array
    {
        return ['name' => $username];
    }

    public function delete(int $id): void {}
}
