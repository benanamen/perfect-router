<?php declare(strict_types=1);

namespace PerfectApp\Controllers;

use PerfectApp\Routing\Route;
use PerfectApp\Services\UserService;


class UserController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/users', ['GET'])]
    public function getUsers(): void
    {
        $users = $this->userService->getAllUsers();
        header('Content-Type: application/json');
        echo json_encode($users);
    }

    #[Route('/user/(\d+)', ['GET'])]
    public function getUser(int $userId): void
    {
        $user = $this->userService->getUserById($userId);
        header('Content-Type: application/json');
        echo json_encode($user);
    }
}
