<?php declare(strict_types=1);

namespace PerfectApp\Routing;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(public string $path, public array $methods = ['GET'])
    {
    }
}
