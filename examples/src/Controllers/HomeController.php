<?php declare(strict_types=1);

namespace PerfectApp\Controllers;

use PerfectApp\Routing\Route;

class HomeController
{
    #[Route('/home', ['GET'])]
    public function index(): void
    {
        echo "Welcome to the Home Page!";
    }
}
