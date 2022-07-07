<?php

namespace Bpm\Core\Route\Builder;

use Bpm\Core\Route\HttpDelete;
use Bpm\Core\Route\HttpGet;
use Bpm\Core\Route\HttpPost;
use Bpm\Core\Route\HttpPut;
use Bpm\Core\Route\HttpRouteType;
use Bpm\Core\Router\Http\Method;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\Stdlib\ArrayUtils;

class RouteSchemeBuilder implements RouteSchemeBuilderInterface
{
    private \Ds\Vector $routes;

    public function __construct(\Ds\Vector $routes)
    {
        $this->routes = $routes;
    }

    public function build(): array
    {
        $routes = [];
        $routes = ArrayUtils::merge($routes, $this->buildGroup($this->routes->filter(fn($x) => $x instanceof HttpGet)));
        $routes = ArrayUtils::merge($routes, $this->buildGroup($this->routes->filter(fn($x) => $x instanceof HttpPost)));
        $routes = ArrayUtils::merge($routes, $this->buildGroup($this->routes->filter(fn($x) => $x instanceof HttpPut)));
        $routes = ArrayUtils::merge($routes, $this->buildGroup($this->routes->filter(fn($x) => $x instanceof HttpDelete)));

        return $routes;
    }

    private function buildGroup(\Ds\Vector $routes)
    {
        if($routes->isEmpty())
        {
            return [];
        }

        return [
            $routes->first()->getRouteTypeName() => [
                'type' => Method::class,
                'options' => [
                    'verb' => $routes->first()->getRouteTypeName(),
                ],
                'child_routes' => $this->buildChildRoutes($routes)
            ]
        ];
    }

    private function buildChildRoutes(\Ds\Vector $routes)
    {
        $childRoutes = [];

        foreach ($routes as $route)
        {
            $childRoutes[$route->getActionName()] = [
                'type'    => $route->hasConstraints() ? Segment::class : Literal::class,
                'options' => [],
            ];

            $childRoutes[$route->getActionName()]['options']['route'] = $route->compile();

            if($route->hasConstraints())
            {
                $childRoutes[$route->getActionName()]['options']['constraints'] = $route->getConstraints();
            }

            $childRoutes[$route->getActionName()]['options']['defaults'] = [
                'action' => $route->getActionName()
            ];
        }

        return $childRoutes;
    }
}