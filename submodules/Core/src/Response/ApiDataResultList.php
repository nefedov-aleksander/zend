<?php


namespace Bpm\Core\Response;


use Bpm\Core\Response\Exception\BadMethodCallException;
use Zend\Http\Response;

class ApiDataResultList implements ApiDataInterface
{
    private array $data;
    private int $offset;
    private int $limit;
    private int $total;

    public function __construct(array $data, int $offset, int $limit, int $total)
    {
        $this->data = $data;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->total = $total;
    }

    public function getStatusCode(): int
    {
        return Response::STATUS_CODE_200;
    }

    public function setStatusCode(int $code)
    {
        throw new BadMethodCallException("Status code from ApiDataResultList cannot be changed");
    }

    public function getResult(): \stdClass
    {
        return (object) [
            'data' => $this->data,
            'offset' => $this->offset,
            'limit' => $this->limit,
            'total' => $this->total
        ];
    }
}