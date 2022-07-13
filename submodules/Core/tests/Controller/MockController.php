<?php

namespace Bpm\Test\Core\Controller;


use Bpm\Core\Controller\BaseController;

class MockController extends BaseController
{
    public function test(int $id, string $name) {}

    public function delete(int $id) {}

    public function post(int $id) {}

    public function put(int $id) {}

    public function withoutParameterAttribute(\stdClass $query){}

    public function testFromQuery(int $id, MockFromQueryRequest $query){}

    public function testFromPost(int $id, MockFromPostRequest $post){}

    public function testFromBody(int $id, MockFromBodyRequest $body){}

    public function withoutArgumentType($arg){}
}