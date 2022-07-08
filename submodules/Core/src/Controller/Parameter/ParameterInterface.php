<?php


namespace Bpm\Core\Controller\Parameter;


use Zend\Http\Request;

interface ParameterInterface
{
    public function map(Request $request, string $exceptedClass);
}