<?php

namespace Tests\Fixtures;

use PerfectApp\Routing\Route;

class DummyController
{
    #[Route(path: '/some/path', methods: ['GET'])]
    public function someMethod($param1, $param2)
    {
        // Method body can be empty for this test
    }
}
