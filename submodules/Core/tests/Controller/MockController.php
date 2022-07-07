<?php


namespace Bpm\Test\Core\Controller;


use Bpm\Core\Controller\BaseController;

class MockController extends BaseController
{
    public function test(int $id, string $name) {}

    public function delete(int $id) {}

    public function post(int $id, MockPostOrPutRequest $request) {}

    public function put(int $id, MockPostOrPutRequest $request) {}
}