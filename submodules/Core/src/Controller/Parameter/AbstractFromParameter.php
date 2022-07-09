<?php

namespace Bpm\Core\Controller\Parameter;


use Bpm\Core\Controller\Parameter\Exception\InvalidMapperException;
use Zend\Http\Header\ContentType;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;

abstract class AbstractFromParameter
{
    const HEADER_CONTENT_TYPE = 'content-type';

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

    protected function getContentType(Request $request, ContentType $default): string
    {
        $contentType = $request
            ->getHeader(self::HEADER_CONTENT_TYPE, $default)
            ->getFieldValue();

        if (strpos($contentType, ';') !== false) {
            $headerData = explode(';', $contentType);
            $contentType = array_shift($headerData);
        }

        return trim($contentType);
    }
}