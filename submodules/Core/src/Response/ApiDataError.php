<?php


namespace Bpm\Core\Response;


use Bpm\Core\Response\Exception\BadMethodCallException;
use Bpm\Core\Validation\Exception\ValidationInterface;
use Zend\Http\Response;

class ApiDataError implements ApiDataInterface
{
    private ValidationInterface $validation;

    public function __construct(ValidationInterface $validation)
    {
        $this->validation = $validation;
    }

    public function getStatusCode(): int
    {
        return Response::STATUS_CODE_422;
    }

    public function setStatusCode(int $code)
    {
        throw new BadMethodCallException("Status code from ApiDataError cannot be changed");
    }

    public function getResult(): \stdClass
    {
        return (object) [
            'messages' => $this->validation->getMessages()
        ];
    }
}