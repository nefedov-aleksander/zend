<?php


namespace Bpm\Core\Controller;


use Bpm\Common\Str;
use Bpm\Core\Controller\Exception\ArgumentCountError;
use Bpm\Core\Controller\Exception\InvalidArgumentException;
use Bpm\Core\Controller\Parameter\ParameterInterface;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

abstract class BaseController extends AbstractController
{
    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        if (! $request instanceof Request) {
            throw new \Zend\Mvc\Exception\InvalidArgumentException('Expected an HTTP request');
        }

        return parent::dispatch($request, $response);
    }

    public function onDispatch(MvcEvent $e)
    {
        $action = $e->getRouteMatch()->getParam('action');

        if(Str::isNullOrEmpty($action))
        {
            $response = $this->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_405);
            return $response;
        }

        try {
            $method = new \ReflectionMethod($this, $action);
        } catch (\ReflectionException $ex)
        {
            $response = $this->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_405);
            return $response;
        }

        $method->invoke($this, ...$this->getActionArguments($method, $e));
    }

    private function getActionArguments(\ReflectionMethod $method, MvcEvent $e)
    {
        if($method->getNumberOfParameters() == 0)
        {
            return [];
        }

        // проверить параметры контролера на наличие типа hasType, если его нет бросать исключение

        $arguments = [];

        foreach ($method->getParameters() as $parameter) {
            if(!$parameter->hasType())
            {
                throw new InvalidArgumentException("Parameter {$parameter->getName()} has no type in {$method->getDeclaringClass()->getName()}::{$method->getName()}");
            }

            $arguments[] = $parameter->getType()->isBuiltin()
                ? $this->getBuiltinArgument($parameter, $e)
                : $this->getFromArgument($parameter);
        }

        return $arguments;
    }

    private function getBuiltinArgument(\ReflectionParameter $parameter, MvcEvent $e)
    {
        $param = $e->getRouteMatch()->getParam($parameter->getName());

        if ($param === null) {
            throw new ArgumentCountError("Too few arguments to function. Argument '{$parameter->getName()}' not found in route '{$e->getRouteMatch()->getMatchedRouteName()}'");
        }

        return $param;
    }

    public function getFromArgument(\ReflectionParameter $parameter)
    {
        $reflection = new \ReflectionClass($parameter->getType()->getName());

        $attributes = $reflection->getAttributes(ParameterInterface::class, \ReflectionAttribute::IS_INSTANCEOF);

        if(count($attributes) == 0)
        {
            throw new InvalidArgumentException("Non builtin parameter '{$parameter->getName()}' must be declared with the attribute instance of " . ParameterInterface::class);
        }

        $from = array_shift($attributes);

        return $from->newInstance()->map($this->getRequest(), $reflection->getName());
    }
}