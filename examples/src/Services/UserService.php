<?php declare(strict_types=1);

namespace PerfectApp\Services;

class UserService
{
    private array $users = [
        ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com'],
        ['id' => 2, 'name' => 'Bob', 'email' => 'bob@example.com'],
        ['id' => 3, 'name' => 'Charlie', 'email' => 'charlie@example.com'],
    ];

    public function getAllUsers(): array
    {
        return $this->users;
    }

    public function getUserById(int $userId): ?array
    {
        foreach ($this->users as $user) {
            if ($user['id'] === $userId) {
                return $user;
            }
        }
        return null;
    }
}
