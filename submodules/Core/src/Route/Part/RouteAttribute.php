<?php


namespace Bpm\Core\Route\Part;


use Bpm\Common\Pair;
use Bpm\Common\Str;

class RouteAttribute implements RouteConstraintInterface
{
    public const PATTERN = '~^{[a-z_]\w*:[a-z]+}$~';

    private static $constraints = [
        'int' => '\d+',
        'string' => '.+'
    ];

    private Pair $route;

    public function __construct(string $part)
    {
        if(Str::isEmptyOrWhiteSpace($part))
        {
            throw new \InvalidArgumentException("Route attribute part cannot be empty");
        }

        if(Str::isNotMatch($part, self::PATTERN))
        {
            throw new \InvalidArgumentException("Invalid route attribute part {$part}.");
        }

        $explode = explode(':', substr($part, 1, -1));

        list($name, $constraint) = $explode;

        if(!key_exists($constraint, self::$constraints))
        {
            throw new \InvalidArgumentException("Invalid route constraint. Allow constraints: " . implode(array_keys(self::$constraints)));
        }

        $this->route = new Pair($name, self::$constraints[$constraint]);
    }

    public function getConstraint()
    {
        return [$this->route->key => $this->route->value];
    }

    public function __toString()
    {
        return ":{$this->route->key}";
    }
}