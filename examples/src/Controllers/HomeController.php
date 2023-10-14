<?php declare(strict_types=1);

namespace PerfectApp\Controllers;

use PerfectApp\Routing\Route;

class HomeController
{
    #[Route('/', ['GET'])]
    public function index(): void
    {
        echo "Welcome to the Home Page!<br>";
        echo "<a href='/users'>Users</a><br>";
        echo "<a href='/user/2'>User #2</a><br>";
    }
}
