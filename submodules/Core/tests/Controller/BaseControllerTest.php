<?php


namespace Bpm\Test\Core\Controller;


use Bpm\Core\Controller\BaseController;
use PHPUnit\Framework\TestCase;
use Zend\Mvc\Exception\InvalidArgumentException;
use Zend\Mvc\MvcEvent;
use Zend\Router\RouteMatch;
use Zend\Stdlib\Request;

class BaseControllerTest extends TestCase
{

    public function testDispatchBadRequest()
    {
        $this->expectException(InvalidArgumentException::class);

        $controller = $this->getMockForAbstractClass(BaseController::class);

        $controller->dispatch(new Request());
    }

    public function testOnDispatchCheckAction()
    {
        $routeMatch = $this->createMock(RouteMatch::class);
        $routeMatch->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo('action'))
            ->willReturn('test');

        $event = new MvcEvent();
        $event->setRouteMatch($routeMatch);

        $controller = $this->getMockForAbstractClass(BaseController::class);

        $controller->onDispatch($event);
    }

    public function testOnDispatchCallActionOnlyOnce()
    {
        $routeMatch = $this->createMock(RouteMatch::class);
        $routeMatch->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo('action'))
            ->willReturn('test');

        $event = new MvcEvent();
        $event->setRouteMatch($routeMatch);

        $controller = $this->getMockForAbstractClass(BaseController::class);
        $controller->expects($this->once())
            ->method('test');

        $controller->onDispatch($event);
    }
}