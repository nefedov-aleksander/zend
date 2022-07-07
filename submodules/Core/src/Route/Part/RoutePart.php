<?php


namespace Bpm\Core\Route\Part;


use Bpm\Common\Str;

class RoutePart implements RoutePartInterface
{
    private string $part;

    public function __construct($part)
    {
        if(Str::isEmptyOrWhiteSpace($part))
        {
            throw new \InvalidArgumentException('Part cannot be empty');
        }

        $this->part = $part;
    }

    public function __toString()
    {
        return $this->part;
    }
}