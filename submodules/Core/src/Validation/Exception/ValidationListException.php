<?php


namespace Bpm\Core\Validation\Exception;

class ValidationListException extends \Exception implements ValidationInterface
{
    private array $messages;

    public function __construct(array $messages)
    {
        parent::__construct("There are one or more validation errors");

        $this->messages = $messages;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}