<?php


namespace Bpm\Core\Controller;


use Bpm\Common\Str;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\Exception\InvalidArgumentException;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

abstract class BaseController extends AbstractController
{
    public function dispatch(RequestInterface $request, ResponseInterface $response = null)
    {
        if (! $request instanceof Request) {
            throw new InvalidArgumentException('Expected an HTTP request');
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

        $arguments = [];
        if($method->getNumberOfParameters() > 0)
        {
            foreach ($method->getParameters() as $parameter)
            {
                $arguments[] = $e->getRouteMatch()->getParam($parameter->getName());
            }
        }

        $method->invoke($this, ...$arguments);
    }
}