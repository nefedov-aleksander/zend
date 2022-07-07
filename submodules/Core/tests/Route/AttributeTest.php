<?php

namespace Bpm\Test\Core\Route;


use Bpm\Core\Route\ApiVersion;
use Bpm\Core\Route\Route;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    public function testApiVersion()
    {
        $version = new ApiVersion(55);
        $this->assertEquals(55, $version->version);
    }

    public function testRoute()
    {
        $route = new Route('v<version:apiVersion>/test');

        $this->assertEquals('v11/test', $route->create([
            '<version:apiVersion>' => 11
        ]));
    }
}