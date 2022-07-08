<?php

namespace Bpm\Core\Controller\Parameter;


use Bpm\Core\Controller\Parameter\Exception\InvalidMapperException;
use Zend\Stdlib\Parameters;

abstract class AbstractObjectParameter
{
    protected function getMapper(\ReflectionClass $reflection, string $exceptedClass): \ReflectionMethod
    {
        foreach ($reflection->getMethods(\ReflectionMethod::IS_STATIC) as $method)
        {
            if($method->getNumberOfParameters() != 1)
            {
                continue;
            }

            $param = $method->getParameters()[0];

            if(
                $param->getType()->getName() == Parameters::class &&
                $method->getReturnType()->getName() == $exceptedClass)
            {
                return $method;
            }
        }

        throw new InvalidMapperException("Mapper for {$exceptedClass} not found in {$reflection->getName()}");
    }
}