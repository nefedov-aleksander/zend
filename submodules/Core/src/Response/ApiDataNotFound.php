<?php


namespace Bpm\Core\Response;


use Bpm\Core\Response\Exception\BadMethodCallException;
use Zend\Http\Response;

class ApiDataNotFound implements ApiDataInterface
{

    public function getStatusCode(): int
    {
        return Response::STATUS_CODE_404;
    }

    public function setStatusCode(int $code)
    {
        throw new BadMethodCallException("Status code from ApiDataOk cannot be changed");
    }

    public function getResult(): \stdClass
    {
        new \stdClass();
    }
}