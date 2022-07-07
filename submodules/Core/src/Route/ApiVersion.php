<?php

namespace Bpm\Core\Route;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ApiVersion
{
    public int $version;

    public function __construct(int $version)
    {
        $this->version = $version;
    }
}