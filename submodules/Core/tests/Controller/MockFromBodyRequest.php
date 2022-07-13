<?php


namespace Bpm\Test\Core\Controller;

use Bpm\Core\Controller\Parameter\FromBody;

#[FromBody(BaseControllerMapper::class)]
class MockFromBodyRequest
{
    public string $body;

    public static function initExpected($body)
    {
        $expected = new self();
        $expected->body = $body;
        return $expected;
    }
}