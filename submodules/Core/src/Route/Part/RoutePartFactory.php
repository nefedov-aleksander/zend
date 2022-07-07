<?php


namespace Bpm\Core\Route\Part;


use Bpm\Common\Str;

class RoutePartFactory implements RoutePartFactoryInterface
{

    public function create(string $part): RoutePartInterface
    {
        if(Str::isMatch($part, RouteAttribute::PATTERN))
        {
            return new RouteAttribute($part);
        }

        return new RoutePart($part);
    }
}