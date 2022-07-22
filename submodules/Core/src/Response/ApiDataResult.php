<?php


namespace Bpm\Core\Response;


use Bpm\Core\Response\Exception\InvalidArgumentException;
use Zend\Http\Response;

class ApiDataResult implements ApiDataInterface
{
    private int $statusCode = Response::STATUS_CODE_200;

    private object $data;

    public function __construct(object $data, $statusCode = Response::STATUS_CODE_200)
    {
        $this->data = $data;
        $this->setStatusCode($statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $code)
    {
        if(!in_array($code, [Response::STATUS_CODE_200, Response::STATUS_CODE_201]))
        {
            throw new InvalidArgumentException("Http status code {$code} cannot be used from ApiDataResult.");
        }

        $this->statusCode = $code;
    }

    public function getResult(): \stdClass
    {
        return (object)(array) $this->data;
    }
}