<?php


namespace Bpm\Core\Route;


use Bpm\Common\Str;
use Bpm\Core\Route\Part\RouteConstraintInterface;
use Bpm\Core\Route\Part\RoutePartFactoryInterface;
use Ds\Vector;
use Zend\Stdlib\ArrayUtils;

abstract class HttpRoute implements RouteInterface
{
    private $route;
    private string $actionName = '';

    public function __construct(string $route, RoutePartFactoryInterface $factory)
    {
        $this->route = new Vector();

        if(!Str::isEmptyOrWhiteSpace($route))
        {
            foreach (explode('/', $route) as $part)
            {
                $this->route->push($factory->create($part));
            }
        }

    }

    public function compile(): string
    {
        $routes = [];
        foreach ($this->route as $parameter)
        {
            $routes[] = (string) $parameter;
        }

        return implode('/', $routes);
    }

    public function hasConstraints(): bool
    {
        return count($this->getConstraints()) > 0;
    }

    public function getConstraints(): array
    {
        $constraints = [];
        foreach ($this->route as $parameter)
        {
            if($parameter instanceof RouteConstraintInterface)
            {
                $constraints = ArrayUtils::merge($constraints, $parameter->getConstraint());
            }
        }

        return $constraints;
    }

    public function setActionName(string $actionName): RouteInterface
    {
        $this->actionName = $actionName;
        return $this;
    }

    public function getActionName(): string
    {
        if(Str::isNullOrEmpty($this->actionName))
        {
            throw new \LogicException('Action name not set before');
        }

        return $this->actionName;
    }
}