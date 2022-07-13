<?php


namespace Bpm\Core\Controller\Parameter;

use Attribute;
use Zend\Http\Request;

#[Attribute(Attribute::TARGET_CLASS)]
class FromQuery extends AbstractFromParameter implements ParameterInterface
{

    private string $mapper;

    public function __construct(string $mapper)
    {
        $this->mapper = $mapper;
    }

    public function map(Request $request, string $exceptedClass)
    {
        return $this->getMapper($this->mapper, $exceptedClass)->invoke(null, $request->getQuery());
    }
}