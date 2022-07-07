<?php

namespace Bpm\Core\Route;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Route
{
    private $frases = [
        '<version:apiVersion>'
    ];

    private string $route;

    public function __construct(string $route = '')
    {
        $this->route = $route;
    }

    public function create($frases = []): string
    {
        return str_replace(array_keys($frases), array_values($frases), $this->route);
    }
}