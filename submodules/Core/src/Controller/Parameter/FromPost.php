<?php

namespace Bpm\Core\Controller\Parameter;

use Attribute;
use Zend\Http\Request;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FromPost extends AbstractObjectParameter implements ParameterInterface
{
    private string $mapper;

    public function __construct(string $mapper)
    {
        $this->mapper = $mapper;
    }

    public function map(Request $request, string $exceptedClass)
    {
        return $this->getMapper(new \ReflectionClass($this->mapper), $exceptedClass)
            ->invoke(null, $request->getPost());
    }
}