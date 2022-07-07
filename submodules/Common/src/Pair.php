<?php

namespace Bpm\Common;


class Pair
{
    public readonly string $key;
    public readonly mixed $value;

    public function __construct(string $key, mixed $value)
    {
        $this->key = $key;
        $this->value = $value;
    }
}