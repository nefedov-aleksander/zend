<?php


namespace Bpm\Core\Validation\Exception;


interface ValidationInterface
{
    public function getMessages(): array;
}