<?php

namespace Bpm\Test\Core\Controller;

use Bpm\Core\Controller\Parameter\FromPost;

#[FromPost(BaseControllerMapper::class)]
class MockFromPostRequest
{
    public string $data;

    public static function initExpected($data)
    {
        $expected = new self();
        $expected->data = $data;
        return $expected;
    }
}