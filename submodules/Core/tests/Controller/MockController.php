<?php

namespace Bpm\Test\Core\Controller;


use Bpm\Core\Controller\BaseController;
use Bpm\Core\Response\ApiDataResult;

class MockController extends BaseController
{
    public function test(int $id, string $name)
    {
        return new ApiDataResult(new \stdClass());
    }

    public function delete(int $id)
    {
        return new ApiDataResult(new \stdClass());
    }

    public function post(int $id)
    {
        return new ApiDataResult(new \stdClass());
    }

    public function put(int $id)
    {
        return new ApiDataResult(new \stdClass());
    }

    public function withoutParameterAttribute(\stdClass $query){}

    public function testFromQuery(int $id, MockFromQueryRequest $query)
    {
        return new ApiDataResult(new \stdClass());
    }

    public function testFromPost(int $id, MockFromPostRequest $post)
    {
        return new ApiDataResult(new \stdClass());
    }

    public function testFromBody(int $id, MockFromBodyRequest $body)
    {
        return new ApiDataResult(new \stdClass());
    }

    public function withoutArgumentType($arg)
    {
        return new ApiDataResult(new \stdClass());
    }

    public function returnResponse()
    {
        return new ApiDataResult(new \stdClass());
    }

    public function returnResponseStd()
    {
        return new \stdClass();
    }
}