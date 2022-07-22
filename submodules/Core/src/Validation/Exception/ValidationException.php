<?php


namespace Bpm\Core\Validation\Exception;


class ValidationException extends \Exception implements ValidationInterface
{

    public function getMessages(): array
    {
        return [$this->getMessage()];
    }
}