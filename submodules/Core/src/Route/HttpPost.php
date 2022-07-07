<?php

namespace Bpm\Core\Route;

use Attribute;
use Bpm\Core\Route\Part\RoutePartFactory;
use Zend\Stdlib\ArrayUtils;

#[Attribute(Attribute::TARGET_METHOD)]
class HttpPost extends HttpRoute
{
    private array $options = [
        'factory' => RoutePartFactory::class
    ];

    public function __construct(string $route = '', array $options = [])
    {
        $this->options = ArrayUtils::merge($this->options, $options);

        parent::__construct($route, new $this->options['factory']());
    }

    public function getRouteTypeName(): string
    {
        return HttpRouteType::POST->value;
    }
}