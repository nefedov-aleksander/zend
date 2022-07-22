<?php


namespace Bpm\Core\Response;


interface ApiDataInterface
{
    public function getStatusCode(): int;

    public function setStatusCode(int $code);

    public function getResult(): \stdClass;
}