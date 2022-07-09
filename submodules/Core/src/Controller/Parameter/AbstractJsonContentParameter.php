<?php


namespace Bpm\Core\Controller\Parameter;


use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Stdlib\Parameters;

abstract class AbstractJsonContentParameter extends AbstractFromParameter
{
    protected function mapFromJson(\ReflectionMethod $mapper, Request $request)
    {
        $data = Json::decode($request->getContent(), Json::TYPE_ARRAY);

        return $mapper->invoke(null, new Parameters($data));
    }
}