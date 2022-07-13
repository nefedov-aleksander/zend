<?php

namespace Bpm\Test\Core\Controller;

use Bpm\Core\Controller\Parameter\FromQuery;

#[FromQuery(BaseControllerMapper::class)]
class MockFromQueryRequest
{
    public string $name;

    public static function initExpected($name)
    {
        $expected = new self();
        $expected->name = $name;
        return $expected;
    }
}